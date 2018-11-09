<?php

namespace Reliefweb\Api;

/**
 * ReliefWeb API Client Query builder.
 */
class Query {
  /**
   * Internal build.
   *
   * @var array
   */
  protected $build = array();

  /**
   * API resource on which to execute the query.
   *
   * @var string
   */
  protected $resource;

  /**
   * API resource item id.
   *
   * @var string
   */
  protected $id;

  /**
   * @var \Reliefweb\Api\Client
   */
  protected $client;

  /**
   * Build a query for this client.
   *
   * @param \Reliefweb\Api\Client $client
   *   ReliefWeb API client.
   */
  public function __construct($resource = '', $client = NULL) {
    $this->resource = $resource;
    $this->client = $client;
  }

    /**
     * Set or get the client for this query.
     *
     * @param Client $client
     *   ReliefWeb API Client.
     * @return null|Client|Query ReliefWeb API Client for this query.
     *   ReliefWeb API Client for this query.
     */
  public function client(Client $client = NULL) {
    if (isset($client)) {
      $this->client = $client;
      return $this;
    }
    return $this->client;
  }

  /**
   * Set or get the resource for this query.
   *
   * @param string $resource
   *   API resource.
   * @return string
   *   API resource.
   */
  public function resource($resource = '') {
    if (!empty($resource)) {
      $this->resource = $resource;
      return $this;
    }
    return $this->resource;
  }

    /**
     * Set the id the resource item to return.
     *
     * @param integer $id
     *   Id of the resource item to return.
     * @return Query This object.
     *   This object.
     */
  public function id($id) {
    $this->id = $id;
    return $this;
  }

    /**
     * Set the preset for the query.
     *
     * @param string $preset
     *   Query preset.
     * @return Query This object.
     *   This object.
     */
  public function preset($preset) {
    $this->build['preset'] = $preset;
    return $this;
  }

    /**
     * Set the profile for the query.
     *
     * @param string $profile
     *   Query profile.
     * @return Query @this
     */
  public function profile($profile) {
    $this->build['profile'] = $profile;
    return $this;
  }

    /**
     * Set fields to include or exclude from the results.
     *
     * @param array $include
     *   Fields to include.
     * @param array $exclude
     *   Fields to exclude.
     * @return Query This object.
     *   This object.
     */
  public function fields($include = array(), $exclude = array()) {
    if (!empty($include)) {
      if (!empty($this->build['fields']['include'])) {
        $include = array_unique(array_merge($this->build['fields']['include'], $include));
      }
      $this->build['fields']['include'] = $include;
    }
    if (!empty($exclude)) {
      if (!empty($this->build['fields']['exclude'])) {
        $exclude = array_unique(array_merge($this->build['fields']['exclude'], $exclude));
      }
      $this->build['fields']['exclude'] = $exclude;
    }
    return $this;
  }

    /**
     * Add sort options to the query.
     *
     * @param string $order
     *   Field used to order the results.
     * @param string $direction
     *   Direction of the ordering (asc or desc).
     * @return Query This object.
     *   This object.
     */
  public function sort($order, $direction) {
    $this->build['sort'][] = $order . ':' . $direction;
    return $this;
  }

    /**
     * Set the range of the query.
     *
     * @param integer $offset
     *   Offset from which to start returning items
     * @param integer $limit
     *   Maximum number of items to return.
     * @return Query This object.
     *   This object.
     */
  public function range($offset, $limit) {
    $this->build['limit'] = $offset;
    $this->build['offset'] = $limit;
    return $this;
  }

    /**
     * Set the offset of the query.
     *
     * @param integer $offset
     *   Offset from which to start returning items
     * @return Query This object.
     *   This object.
     */
  public function offset($offset) {
    $this->build['offset'] = $offset;
    return $this;
  }

    /**
     * Set the limit of the query.
     *
     * @param integer $limit
     *   Maximum number of items to return.
     * @return Query This object.
     *   This object.
     */
  public function limit($limit) {
    $this->build['limit'] = $limit;
    return $this;
  }

    /**
     * Set the search query.
     *
     * @param string $search
     *   Search query.
     * @param array $fields
     *   Fields on which to perform the request.
     * @param string $operator
     *   Default operator (OR or AND) for the query.
     * @return Query This object.
     *   This object.
     */
  public function search($search, $fields = array(), $operator = '') {
    if (!empty($fields)) {
      $this->build['query']['fields'] = $fields;
    }
    if (!empty($operator)) {
      $this->build['query']['operator'] = $operator;
    }
    $this->build['query']['value'] = $search;
    return $this;
  }

    /**
     * Set thefields for the search query.
     *
     * @param array $fields
     *   Fields on which to perform the request.
     * @return Query This object.
     *   This object.
     */
  public function searchFields($fields) {
    $this->build['query']['fields'] = $fields;
    return $this;
  }

    /**
     * Set the filter for the query.
     *
     * @param Filter $filter
     *   Query filter.
     * @return Query This object.
     *   This object.
     */
  public function filter(Filter $filter) {
    $this->build['filter'] = $filter->build();
    return $this;
  }

    /**
     * Add a facet to return along with the query results.
     *
     * @param \Reliefweb\Api\Facet $facet
     *   Facet.
     * @return Query This object.
     *   This object.
     */
  public function facets(Facet $facet) {
    $this->build['facets'][] = $facet->build();
    return $this;
  }

  /**
   * Return the built API data query.
   *
   * @return array
   *   API data query.
   */
  public function build() {
    // Limit to supported parameters for individual entity queries.
    if (!empty($this->id)) {
      return array_intersect_key($this->build, array_flip(array('fields', 'preset', 'profile')));
    }
    return $this->build;
  }

  /**
   * Execute the query against the client.
   *
   * @param boolean $raw
   *   Indicates whether to return the raw data or the wrapped data.
   * @return \Reliefweb\Api\Results|array
   *   Data returned by the API.
   */
  public function execute($raw = FALSE) {
    $data = NULL;
    if (isset($this->client) && !empty($this->resource)) {
      $path = $this->resource . (!empty($this->id) ? '/' . $this->id : '');
      $method = !empty($this->id) ? 'GET' : 'POST';
      $data = $this->client->query($path, $this->build(), $method);
    }
    $results = new Results($data);

    return $raw ? $results->raw() : $results;
  }
}
