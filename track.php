<?php

require 'core/freelance_ru.class.php';

$results = [
	'error' => true,
	'error_msg' => 'Connection error',
];

if (isset($_POST['freelance_ru_login'], $_POST['freelance_ru_pass']))
{
	$user['freelance_ru'] = [
		'login' => $_POST['freelance_ru_login'],
		'pass' => $_POST['freelance_ru_pass']
	];

	$freelance_ru = new Freelance_ru();
	if ($freelance_ru->login($user['freelance_ru']['login'], $user['freelance_ru']['pass']))
	{
		$data = $freelance_ru->get_task();

		if ($data)
		{
			$results = [
				'error' => false,
				'error_msg' => null,
				'data' => [
					'freelance_ru' => $data
				]
			];
		}
		else
		{
			$results['error_msg'] = 'no new tasks';
		}
	}

} else
{
	$results['error_msg'] = 'no login/pass';
}

echo json_encode($results);
header('Content-type: application/json');