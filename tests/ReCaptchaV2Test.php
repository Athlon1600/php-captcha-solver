<?php /** @noinspection PhpUnhandledExceptionInspection */

use CaptchaSolver\TwoCaptcha\InRequest;
use PHPUnit\Framework\TestCase;

class ReCaptchaV2Test extends TestCase
{
    protected $api_key;

    protected function setUp(): void
    {
        $key = getenv('TWO_CAPTCHA_API_KEY');

        if ($key) {
            $this->api_key = $key;
        }
    }

    public function test_error_when_no_key()
    {
        $client = new \CaptchaSolver\TwoCaptcha\Client(null);

        $response = $client->send(new InRequest());

        $this->assertEquals(0, $response->getStatus());
        $this->assertNotNull($response->getError(), 'error_text was expected from a response! ' . $response);
    }

    public function test_solves_google_captcha_demo()
    {
        $solver = new \CaptchaSolver\TwoCaptcha\Client([
            'key' => $this->api_key
        ]);

        $browser = new \Curl\Client();

        $response = $browser->get("https://www.google.com/recaptcha/api2/demo");

        $url = $response->info->url;
        $site_key = \CaptchaSolver\Utils::findSiteKey($response->body);

        $this->assertNotEmpty($site_key, 'Could not find site_key for site...');

        $captcha_send_response = $solver->send(new InRequest([
            'pageurl' => $url,
            'googlekey' => $site_key
        ]));

        $this->assertSame(1, $captcha_send_response->getStatus(), 'Status 0 was received when trying to submit a captcha: ' . $captcha_send_response);

        $request_id = $captcha_send_response->getResult();

        $this->assertNotEmpty($request_id, 'Captcha request to /in.php did no produce a result...' . $captcha_send_response);

        $captcha_get_result_response = null;

        // usually good enough
        $timeout = 120;

        while ($timeout > 0) {

            sleep(30);

            $captcha_get_result_response = $solver->getResult($request_id);

            if ($captcha_get_result_response->hasSolution()) {
                break;
            }

            $timeout -= 30;
        }

        $solution = $captcha_get_result_response->getSolution();

        $this->assertNotEmpty($solution, $captcha_get_result_response);

        $post_response = $browser->post('https://www.google.com/recaptcha/api2/demo', [
            'g-recaptcha-response' => $solution
        ]);

        $this->assertEquals(200, $post_response->status);

        $this->assertStringContainsString("recaptcha-success", $post_response->body);
        $this->assertStringNotContainsString("recaptcha-error-message", $post_response->body);
    }
}
