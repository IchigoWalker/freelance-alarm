<?php

class Freelance_ru {

	private static $auth_data = [
		'login'       => null,
		'passwd'      => null,
		'remember_me' => 'on',
		'auth'        => 'auth',
		'return_url'  => '/login/?auth=logout'
	];

	private static $filter_data = [
		'spec_id_4'   => '4',
		'spec_id_133' => '133',
		'spec_id_116' => '116',
		'spec_id_540' => '540',
		'filter_post' => '1'
	];

	private $unseen_limit = 3;

	public function __set($property, $value)
	{
		if (property_exists($this, $property))
		{
			$this->$property = $value;
		}
	}

	private function curl_ready($curl, $url, $params)
	{
		$options = [
			CURLOPT_URL            => $url,
			CURLOPT_USERAGENT      => 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.205 Safari/534.16',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => $params,
			CURLOPT_COOKIEFILE     => __DIR__ . '/cookies/freelance_ru/' . self::$auth_data['login'] . '.cookie',
			CURLOPT_COOKIEJAR      => __DIR__ . '/cookies/freelance_ru/' . self::$auth_data['login'] . '.cookie'
		];

		curl_setopt_array($curl, $options);

		return $curl;
	}

	public function login($login, $pass)
	{
		self::$auth_data['login'] = $login;
		self::$auth_data['passwd'] = $pass;

		$curl = curl_init();
		$curl = $this->curl_ready($curl, 'https://freelance.ru/login/', self::$auth_data);
		curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		return $code === 200;
	}

	public function get_task()
	{
		$curl = curl_init();
		$curl = $this->curl_ready($curl, 'https://freelance.ru/projects/filter/', self::$filter_data);

		$page = curl_exec($curl);

		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($code === 200)
		{
			$dom = new DOMDocument();
			@$dom->loadHTML($page);
			$xpath = new DOMXpath($dom);
			$div = $xpath->query('//div[contains(@class, "unseen")]//a[contains(@class, "descr")]');

			$limit = ($this->unseen_limit < $div->length) ? $this->unseen_limit : $div->length;

			$output = null;
			for ($i = 0; $i < $limit; ++ $i)
			{
				$output[] = $div->item($i)->getAttribute('href');
			}

			return $output;

		}

		return false;
	}
}