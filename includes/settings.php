<?php

if (!class_exists('TR_WC_Settings')) {
  class TR_WC_Settings
  {
    const BASE_URL = 'https://staging.shipping.trunkrs.app';
    const TRACK_TRACE_BASE_URL = 'https://parcel.trunkrs.nl/';
    const API_VERSION = 'v1';

    const OPTION_KEY = 'wc_tr_plugin-settings';

    /**
     * @var array|null
     */
    private static $_options = null;

    private static function getOptions(): array
    {
      if (!self::$_options) {
        $storedOption = get_option(self::OPTION_KEY);
        self::$_options = empty($storedOption) ? [] : $storedOption;
      }

      return self::$_options;
    }

    private static function getSingleOption(string $key)
    {
      $options = self::getOptions();

      return array_key_exists($key, $options) ? $options[$key] : null;
    }

    private static function pushOption(string $key, $value)
    {
      $options = self::getOptions();
      $options[$key] = $value;

      update_option(self::OPTION_KEY, $options);
      self::$_options = $options;
    }

    /**
     * Gets the full resource url for the specified resource.
     * @param string $resource The resource to be put into the url.
     * @return string The full url for the resource.
     */
    public static function getResourceUrl(string $resource): string
    {
      return sprintf(
        '%s/%s/%s',
        self::BASE_URL,
        self::API_VERSION,
        ltrim($resource, '/')
      );
    }

    /**
     * Reflects whether the plugin has been configured.
     * @return bool Value reflecting config status
     */
    public static function isConfigured(): bool
    {
      return self::getSingleOption('isConfigured') ?? false;
    }

    /**
     * Retrieves the integration details for the current configuration.
     * @return array The integration details.
     */
    public static function getIntegrationDetails()
    {
      return self::getSingleOption('integrationDetails');
    }

    /**
     * Retrieves the access token.
     * @return string The access token.
     */
    public static function getAccessToken()
    {
      return self::getSingleOption('accessToken');
    }

    /**
     * Retrieves whether to use dark adjusted check-out content.
     * @return bool Flag reflecting whether to adjust for dark content.
     */
    public static function getUseDark(): bool
    {
      return self::getSingleOption('useDark') ?? false;
    }

    /**
     * Gets whether to enable track & trace links in order confirmation emails.
     * @return bool Flag whether to enable track & trace links.
     */
    public static function getUseTrackTraceLinks(): bool {
      return self::getSingleOption('useTrackTraceLinks') ?? false;
    }

    /**
     * Gets whether to enable track & trace actions in the my account page.
     * @return bool Flag whether to enable track & trace account actions.
     */
    public static function getUseAccountActions(): bool {
      return self::getSingleOption('useTrackTraceActions') ?? false;
    }

    /**
     * Sets the flag whether the plugin has been configured.
     * @param $isConfigured bool Flag showing whether the plugin was configured.
     */
    public static function setConfigured(bool $isConfigured)
    {
      self::pushOption('isConfigured', $isConfigured);
    }

    /**
     * Saves the integrations details into the store.
     * @param array $details The integration details.
     */
    public static function setIntegrationDetails(array $details)
    {
      self::pushOption('integrationDetails', $details);
    }

    /**
     * Saves the access token into the store.
     * @param string $accessToken The access token.
     */
    public static function setAccessToken(string $accessToken)
    {
      self::pushOption('accessToken', $accessToken);
    }

    /**
     * Sets whether to use a dark adjusted logo on check-out
     * @param bool $isUseDark The flag whether to use dark adjusted content
     */
    public static function setUseDark(bool $isUseDark)
    {
      self::pushOption('useDark', $isUseDark);
    }

    /**
     * Sets whether to enable track & trace links in order confirmation emails.
     * @param bool $isUseEmailLink
     */
    public static function setUseEmailLink(bool $isUseEmailLink) {
      self::pushOption('useTrackTraceLinks', $isUseEmailLink);
    }

    /**
     * Sets whether to enable track & trace actions in the my account area.
     * @param bool $isUseAccountAction
     */
    public static function setUseAccountActions(bool $isUseAccountAction) {
      self::pushOption('useTrackTraceActions', $isUseAccountAction);
    }
  }
}
