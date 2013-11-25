<?php
namespace jief;

use Guzzle\Http\Client;
use Guzzle\Plugin\Oauth\OauthPlugin;

Class Linkedin
{
  private $consumer_key;
  private $consumer_secret;
  private $token;
  private $token_secret;

  protected $client;

  function __construct($config)
  {
    $this->consumer_key = $config['linkedin']['consumer_key'];
    $this->consumer_secret = $config['linkedin']['consumer_secret'];
    $this->token = $config['linkedin']['token'];
    $this->token_secret = $config['linkedin']['token_secret'];

    $this->auth();
  }

  private function auth()
  {
    $this->client = new Client('http://api.linkedin.com/v1');
    $oauth = new OauthPlugin(array(
      'consumer_key'    => $this->consumer_key,
      'consumer_secret' => $this->consumer_secret,
      'token'           => $this->token,
      'token_secret'    => $this->token_secret
    ));

    $this->client->addSubscriber($oauth);
  }

  protected function call($method)
  {
    return $this->client->get('people/~:(' . $method . ')?format=json')->send()->json();
  }

  public function getSkills()
  {
    $response = $this->call('skills');
    $skills = array();

    if(isset($response['skills']) && isset($response['skills']['values'])) {
      foreach($response['skills']['values'] as $skill) {
        $skills[] = $skill['skill']['name'];
      }
    }

    return $skills;
  }

  public function getPositions($onlyCurrent = true)
  {
    $response = $this->call('positions');
    $positions = array();

    if(isset($response['positions']) && isset($response['positions']['values'])) {
      foreach($response['positions']['values'] as $position) {
        if(($onlyCurrent && $position['isCurrent'] == 1) || ! $onlyCurrent) {
          $positions[] = $position;
        }
      }
    }

    return $positions;
  }

  public function getName($call = 'first-name,last-name,headline')
  {
    $response = $this->call($call);
    $name = $headline = "";

    if(isset($response["firstName"])) {
      $name .= $response["firstName"];
    }

    if(isset($response["lastName"])) {
      if(trim($name) != "") {
        $name .= " ";
      }

      $name .= $response["lastName"];
    }

    if(isset($response['headline'])) {
      $headline = $response['headline'];
    }

    return array('name' => $name, 'headline' => $headline);
  }
}
