<?php
namespace Craft;

class LocalePermissionsPlugin extends BasePlugin
{
    public function getName()
    {
        return Craft::t('Locale Permissions');
    }

    public function getDescription()
    {
        return 'Set control panel read/write access by locale on entries, categories and global fields. ';
    }

    public function getVersion()
    {
        return '1.0';
    }

    public function getDeveloper()
    {
        return 'Firstborn';
    }

    public function getDeveloperUrl()
    {
        return 'http://www.firstborn.com';
    }

    public function init()
    {
        //template hooks
        craft()->templates->hook('cp.entries.edit', function (&$context) {
            if ($this->embedReadOnlyScripts($context['entry']->locale )){
                $this->embedScripts();
            }
        });

         craft()->templates->hook('cp.categories.edit', function (&$context) {
            if ($this->embedReadOnlyScripts($context['category']->locale )){
                $this->embedScripts();
            }
        });

        if (craft()->request->isCpRequest() && craft()->userSession->isLoggedIn() && craft()->request->getSegment(1) == 'globals' && !craft()->request->isAjaxRequest()) {
            craft()->getUrlManager()->parseUrl(craft()->request);
            $routeParams = craft()->urlManager->getRouteParams();
            if (isset($routeParams['variables']['localeId'])){
                $locale = $routeParams['variables']['localeId'];
            } else {
                $locale = craft()->getLocale()->id;
            }

            if ($this->embedReadOnlyScripts($locale )){
               $this->embedScripts();
            }
        }
    }

    private function embedReadOnlyScripts($locale)
    {
        // get current user
        $user = craft()->userSession->getUser();

        // admins should never be restricted to read only
        if ($user->admin) {
            return false;
        }

        if ($locale === null) {
            return false;
        }

        // does user have edit access to entries under this locale?
        return $user->can('canEditLocaleEntries_' . $locale) ? false : true;
    }

    private function embedScripts(){
        craft()->templates->includeJsResource('localepermissions/js/locale-permissions.js');
        craft()->templates->includeJs("new Craft.LocalePermissions('" . Craft::t('Read Only Access') . "');");
        craft()->templates->includeCssResource('localepermissions/css/style.css');
    }

    public function registerUserPermissions()
    {
        $userPermissions = array();
        foreach (craft()->i18n->getSiteLocales() AS $locale) {
            $settingName = 'canEditLocaleEntries_' . $locale->id;
            $userPermissions[$settingName] = array('label' => 'Can edit ' . $locale->getName() . ' entries');
        }

        return $userPermissions;
    }
}