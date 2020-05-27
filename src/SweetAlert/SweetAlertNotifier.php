<?php

namespace TeunVos\SweetAlert;

class SweetAlertNotifier
{
    const ICON_WARNING = 'warning';
    const ICON_ERROR = 'error';
    const ICON_SUCCESS = 'success';
    const ICON_INFO = 'info';
    const ICON_QUESTION = 'question';

    /**
     * @var \TeunVos\SweetAlert\SessionStore
     */
    protected $session;

    /**
     * Configuration options.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Create a new SweetAlertNotifier instance.
     *
     * @param \TeunVos\SweetAlert\SessionStore $session
     */
    public function __construct(SessionStore $session)
    {
        $this->session = $session;

        $this->setDefaultConfig();
    }

    /**
     * Sets all default config options for an alert.
     *
     * @return void
     */
    protected function setDefaultConfig()
    {
        $this->setConfig([
            'timer' => config('sweet-alert.autoclose'),
        ]);
    }

    /**
     * Display an alert message with a text and an optional title.
     *
     * By default the alert is not typed.
     *
     * @param string $text
     * @param string $title
     * @param string $icon
     *
     * @return \TeunVos\SweetAlert\SweetAlertNotifier $this
     */
    public function message($text = '', $title = null, $icon = null)
    {
        $this->config['text'] = $text;

        if (! is_null($title)) {
            $this->config['title'] = $title;
        }

        if (! is_null($icon)) {
            $this->config['type'] = $icon;
        }

        return $this;
    }

    /**
     * Display a not typed alert message with a text and a title.
     *
     * @param string $text
     * @param string $title
     *
     * @return \TeunVos\SweetAlert\SweetAlertNotifier $this
     */
    public function basic($text, $title)
    {
        $this->message($text, $title);

        return $this;
    }

    /**
     * Display an info typed alert message with a text and an optional title.
     *
     * @param string $text
     * @param string $title
     *
     * @return \TeunVos\SweetAlert\SweetAlertNotifier $this
     */
    public function info($text, $title = '')
    {
        $this->message($text, $title, self::ICON_INFO);

        return $this;
    }

    /**
     * Display a success typed alert message with a text and an optional title.
     *
     * @param string $text
     * @param string $title
     *
     * @return \TeunVos\SweetAlert\SweetAlertNotifier $this
     */
    public function success($text, $title = '')
    {
        $this->message($text, $title, self::ICON_SUCCESS);

        return $this;
    }

    /**
     * Display an error typed alert message with a text and an optional title.
     *
     * @param string $text
     * @param string $title
     *
     * @return \TeunVos\SweetAlert\SweetAlertNotifier $this
     */
    public function error($text, $title = '')
    {
        $this->message($text, $title, self::ICON_ERROR);

        return $this;
    }

    /**
     * Display a warning typed alert message with a text and an optional title.
     *
     * @param string $text
     * @param string $title
     *
     * @return \TeunVos\SweetAlert\SweetAlertNotifier $this
     */
    public function warning($text, $title = '')
    {
        $this->message($text, $title, self::ICON_WARNING);

        return $this;
    }

    /**
     * Set the duration for this alert until it autocloses.
     *
     * @param int $milliseconds
     * @param bool $progressBar
     *
     * @return \TeunVos\SweetAlert\SweetAlertNotifier $this
     */
    public function autoclose($milliseconds = null, $progressBar = true)
    {
        if (! is_null($milliseconds)) {
            $this->config['timer'] = $milliseconds;
            $this->config['timerProgressBar'] = $progressBar;
        }
        return $this;
    }

    /**
     * Add a cancel button to the alert.
     *
     * @param string $buttonText
     * @param array  $overrides
     *
     * @return \TeunVos\SweetAlert\SweetAlertNotifier $this
     */
    public function cancelButton($buttonText, $color = null, $ariaLabel = null)
    {
        $this->config['showCancelButton'] = true;
        $this->config['confirmCancelText'] = $buttonText;
        isset($color) ?? $this->config['cancelButtonColor'] = $color;
        isset($ariaLabel) ?? $this->config['cancelButtonAriaLabel'] = $ariaLabel;

        $this->closeOnClickOutside(false);
        $this->removeTimer();

        return $this;
    }

    /**
     * Add a new custom button to the alert.
     *
     * @param string $buttonText
     * @param array  $overrides
     *
     * @return \TeunVos\SweetAlert\SweetAlertNotifier $this
     */
    public function confirmButton($buttonText, $color = null, $ariaLabel = null)
    {
        $this->config['showConfirmButton'] = true;
        $this->config['confirmButtonText'] = $buttonText;
        isset($color) ?? $this->config['confirmButtonColor'] = $color;
        isset($ariaLabel) ?? $this->config['confirmButtonAriaLabel'] = $ariaLabel;

        $this->closeOnClickOutside(false);
        $this->removeTimer();

        return $this;
    }

    /**
     * Toggle close the alert message when clicking outside.
     *
     * @param string $buttonText
     *
     * @return \TeunVos\SweetAlert\SweetAlertNotifier $this
     */
    public function closeOnClickOutside($value = true)
    {
        $this->config['allowOutsideClick'] = $value;

        return $this;
    }

    /**
     * Make this alert persistent with a confirmation button.
     *
     * @param string $buttonText
     *
     * @return \TeunVos\SweetAlert\SweetAlertNotifier $this
     */
    public function persistent($buttonText = 'OK', $color = null, $ariaLabel = null)
    {
        $this->confirmButton($buttonText, $color, $ariaLabel);
        $this->closeOnClickOutside(false);
        $this->removeTimer();

        return $this;
    }

    /**
     * Remove the timer config option.
     *
     * @return void
     */
    protected function removeTimer()
    {
        if (array_key_exists('timer', $this->config)) {
            unset($this->config['timer']);
        }
    }

    /**
     * Flash the current alert configuration to the session store.
     *
     * @return void
     */
    protected function flashConfig()
    {
        $this->session->remove('sweet_alert');

        foreach ($this->config as $key => $value) {
            $this->session->flash("sweet_alert.{$key}", $value);
        }

        $this->session->flash('sweet_alert.alert', $this->buildJsonConfig());
    }

    /**
     * Build the configuration as Json.
     *
     * @return string
     */
    protected function buildJsonConfig()
    {
        return json_encode($this->config);
    }

    /**
     * Return the current alert configuration.
     *
     * @return array
     */
    public function getConfig($key = null)
    {
        if (is_null($key)) {
            return $this->config;
        }

        if (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }
    }

    /**
     * Customize alert configuration "by hand".
     *
     * @return array
     */
    public function setConfig($config = [])
    {
        $this->config = array_merge($this->config, $config);

        return $this;
    }

    /**
     * Return the current alert configuration as Json.
     *
     * @return string
     */
    public function getJsonConfig()
    {
        return $this->buildJsonConfig();
    }

    /**
     * Handle the object's destruction.
     *
     * @return void
     */
    public function __destruct()
    {
        $this->flashConfig();
    }
}
