<?php

namespace TeunVos\SweetAlert;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \TeunVos\SweetAlert\SweetAlertNotifier message(string $text = '', $title = null, $icon = null)
 * @method static \TeunVos\SweetAlert\SweetAlertNotifier basic(string $text, string $title)
 * @method static \TeunVos\SweetAlert\SweetAlertNotifier info(string $text, string $title = '')
 * @method static \TeunVos\SweetAlert\SweetAlertNotifier success(string $text, string $title = '')
 * @method static \TeunVos\SweetAlert\SweetAlertNotifier error(string $text, string $title = '')
 * @method static \TeunVos\SweetAlert\SweetAlertNotifier warning(string $text, string $title = '')
 */
class SweetAlert extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'teunvos.sweet-alert';
    }
}
