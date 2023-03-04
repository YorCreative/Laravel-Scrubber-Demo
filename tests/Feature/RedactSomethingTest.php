<?php

namespace Tests\Feature;

use Tests\TestCase;
use YorCreative\Scrubber\Scrubber;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use YorCreative\Scrubber\Handlers\ScrubberTap;
use App\Scrubber\RegexCollection\SomethingRegex;
use YorCreative\Scrubber\Repositories\RegexRepository;

class RedactSomethingTest extends TestCase
{
    public function testItCanSuccessfullyRedactSomething(): void
    {
        $custom_regex_loader = [
            SomethingRegex::class
        ];
        Config::set('scrubber.regex_loader', $custom_regex_loader);

        $config_regex_loader = Config::get('scrubber.regex_loader');

        $this->assertEquals($custom_regex_loader, $config_regex_loader);
        $this->assertCount(1, $config_regex_loader);

        foreach(Config::get('logging.channels') as $channel) {
            $this->assertArrayHasKey('tap', $channel);
            $this->assertEquals(ScrubberTap::class, $channel['tap'][0]);
        };

        $sanitized = Scrubber::processMessage(
            App::make(RegexRepository::class)
                ->getRegexCollection()
                ->get('something_regex')
                ->getTestableString()
        );

        $this->assertEquals(Config::get('scrubber.redaction'), $sanitized);
    }

    public function testItCanRedactJWTOutOfSentence(): void
    {
        $regexRepository = App::make(RegexRepository::class);

        $testableToken = $regexRepository->getRegexCollection()
            ->get('json_web_token')
            ->getTestableString();

        $originalMessage = 'Hey, sorry im late here is the token ' . $testableToken . ' let me know if you need anything else!';
        $expectedSanitizedMessage = 'Hey, sorry im late here is the token ' . Config::get('scrubber.redaction') . ' let me know if you need anything else!';

        $sanitized = Scrubber::processMessage($originalMessage);

        $this->assertEquals($expectedSanitizedMessage, $sanitized);
    }
}
