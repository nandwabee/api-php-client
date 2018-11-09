<?php

namespace Reliefweb\Api;

/**
 * ReliefWeb API Client.
 */
class Client {
  /**
   * API URL.
   *
   * @var string
   */
  protected $url;

  /**
   * Application name.
   *
   * @var string
   */
  protected $appname;

  /**
   * Build client.
   *
   * @param string $host
   *   API base url.
   */
  public function __construct($url = 'https://api.reliefweb.int/v1') {
    $this->url = $url;
  }

    /**
     * Set the name of the application or website using the API.
     *
     * @param string $appname
     *   Application name.
     *
     * @return Client
     */
  public function appname($appname = "") {
    $this->appname = $appname;

    return $this;
  }

  /**
   * Query the API.
   *
   * Note: CURL is required.
   *
   * @param string $resource
   *   Resource name.
   * @param \Reliefweb\Api\Query|array $data
   *   Data to pass to the API.
   * @param integer $method
   *   POST or GET method.
   * @param integer $timeout
   *   Request timeout.
   * @return array
   *   Data received by the API or null in case of error.
   */
  public function query($resource, $data = array(), $method = 'POST', $timeout = 2) {
    $url = $this->url . '/' . $resource;

    // Set the appname parameter.
    $url .= '?appname=' . (!empty($this->appname) ? urlencode($this->appname) : 'rw-api-php-client');

    if ($data instanceof Query) {
      $data = $data->build();
    }

    // Add the GET data to the url.
    if ($method === 'GET') {
      if (is_array($data)) {
        $data = http_build_query($data, '', '&');
      }
      $url .= !empty($data) ? '&' . $data : '';
    }

    $curl = curl_init($url);

    // Add the POST data to the request.
    if ($method === 'POST') {
      if (is_array($data)) {
        $data = json_encode($data);
      }
      $headers = array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data),
      );


      curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    }

    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
    $result = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    return $status === 200 ? json_decode($result, TRUE) : NULL;
  }

  /**
   * Shortcut to query reports.
   *
   * @param \Reliefweb\Api\Query|array $data
   *   Data to pass to the API.
   * @return array
   *   Data received by the API.
   */
  public function reports($data = array()) {
    return $this->query('reports', $data);
  }

  /**
   * Shortcut to query jobs.
   *
   * @param \Reliefweb\Api\Query|array $data
   *   Data to pass to the API.
   * @return array
   *   Data received by the API.
   */
  public function jobs($data = array()) {
    return $this->query('jobs', $data);
  }

  /**
   * Shortcut to query training.
   *
   * @param \Reliefweb\Api\Query|array $data
   *   Data to pass to the API.
   * @return array
   *   Data received by the API.
   */
  public function training($data = array()) {
    return $this->query('training', $data);
  }

  /**
   * Shortcut to query sources.
   *
   * @param \Reliefweb\Api\Query|array $data
   *   Data to pass to the API.
   * @return array
   *   Data received by the API.
   */
  public function sources($data = array()) {
    return $this->query('sources', $data);
  }

  /**
   * Shortcut to query countries.
   *
   * @param \Reliefweb\Api\Query|array $data
   *   Data to pass to the API.
   * @return array
   *   Data received by the API.
   */
  public function countries($data = array()) {
    return $this->query('countries', $data);
  }

  /**
   * Shortcut to query disasters.
   *
   * @param \Reliefweb\Api\Query|array $data
   *   Data to pass to the API.
   * @return array
   *   Data received by the API.
   */
  public function disasters($data = array()) {
    return $this->query('disasters', $data);
  }
}
