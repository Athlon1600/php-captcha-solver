<?php

require("../vendor/autoload.php");

if (isset($_POST['action'])) {

    $action = $_POST['action'];
    $key = $_POST['api_key'];

    $proxy = isset($_POST['proxy']) ? $_POST['proxy'] : null;

    $captcha = new \CaptchaSolver\TwoCaptcha([
        'key' => $key,
        'proxy' => $proxy
    ]);

    if ($action == 'send') {

        $googlekey = $_POST['googlekey'];
        $pageurl = $_POST['pageurl'];

        echo $captcha->sendReCaptchaV2($googlekey, $pageurl);

    } else if ($action == 'get') {

        $request_id = $_POST['request_id'];

        echo $captcha->getReCaptchaV2($request_id);
    }

    exit;
}

?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<form action="/">
    <fieldset>

        <label for="api_key">API Key:</label>
        <input name="api_key" id="api_key" size="100" value="">

        <p></p>

        <label for="proxy">Proxy:</label>
        <input name="proxy" id="proxy" size="100" value="">

        <p></p>

        <label for="googlekey">googlekey:</label>
        <input name="googlekey" id="googlekey" size="100" value="6Le-wvkSAAAAAPBMRTvw0Q4Muexq9bi0DJwx_mJ-">

        <p></p>

        <label for="pageurl">pageurl:</label>
        <input name="pageurl" id="pageurl" size="100" value="https://www.google.com/recaptcha/api2/demo">

        <p></p>
        <input type="submit" value="Submit">

    </fieldset>

</form>

<pre id="console"></pre>

<script>

    function _log(line) {
        $("#console").append(line + '\n');
    }

    function _poll(request_id) {

        var counter = 0;

        var data = get_form_data();
        data['action'] = 'get';
        data['request_id'] = request_id;

        $.post(window.location, data, function (res) {

            if (++counter < 10) {

                if (res) {
                    _log('Captcha solved!');

                    $("#captcha_response").val(res);
                    enable_button(true);

                    return;
                }

                _log('Polling...');

                setTimeout(function () {
                    _poll(request_id);
                }, 6000);

            } else {

                _log('Timed out after 60 seconds...');
                enable_button(true);
            }

        });
    }

    function get_form_data() {

        var data = {};

        $("form").serializeArray().forEach(function (val) {
            data[val['name']] = val['value'];
        });

        return data;
    }

    function enable_button(enabled) {
        var btn = $("form").find('[type=submit]');

        if (enabled) {
            btn.prop('disabled', false);
        } else {
            btn.prop('disabled', true);
        }
    }

    $(function () {

        $("form").submit(function (e) {
            e.preventDefault();

            $(this).find('[type=submit]').prop('disabled', true);

            var data = get_form_data();
            data['action'] = 'send';

            $.post(window.location, data, function (res) {

                var id = parseInt(res);

                if (isNaN(id)) {
                    _log('Error!');
                    _log(res);

                    enable_button(true);

                } else {
                    _log('Request sent! ID: ' + id);
                    _poll(id);
                }
            });

            return false;
        })
    });
</script>


<h3>g-recaptcha-response</h3>

<textarea id="captcha_response" rows="5" cols="100"></textarea>

