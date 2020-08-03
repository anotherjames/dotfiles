<?php

class SimplesamlValetDriver extends BasicValetDriver
{
  /**
   * Determine if the driver serves the request.
   *
   * @param  string  $sitePath
   * @param  string  $siteName
   * @param  string  $uri
   * @return bool
   */
  public function serves($sitePath, $siteName, $uri)
  {
    return 0 === stripos( $uri, '/simplesaml/' );
  }

  /**
   * Get the fully resolved path to the application's front controller.
   *
   * @param  string  $sitePath
   * @param  string  $siteName
   * @param  string  $uri
   * @return string
   */
  public function frontControllerPath($sitePath, $siteName, $uri)
  {
    $_SERVER['PHP_SELF']    = $uri;
    $_SERVER['SERVER_ADDR'] = '127.0.0.1';
    $_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'];
    preg_match( '#^(/simplesaml/[^\.]+\.php)(.*)#', $uri, $matches );
    $_SERVER['PATH_INFO'] = $matches[2];
    return $sitePath . $matches[1];
  }

  /**
   * Determine if the incoming request is for a static file.
   *
   * @param  string  $sitePath
   * @param  string  $siteName
   * @param  string  $uri
   * @return string|false
   */
  public function isStaticFile($sitePath, $siteName, $uri)
  {
    if ($this->isActualFile($staticFilePath = $sitePath . $uri)) {
      return $staticFilePath;
    }

    return false;
  }
}
