<?php
/** @var \Codeception\Scenario $scenario */

use Codeception\Util\HttpCode;

$I = new ApiTester($scenario);
$files_root = realpath(__DIR__ . '/../files') . '/';

$I->wantTo('Check rename file');

$I->recurseCopy($files_root . 'folder1', $files_root . 'temp');

try {
	$I->sendGet('?action=folderRename&source=test&name=folder1&path=/&newname=folder2');

	$I->seeResponseCodeIs(HttpCode::OK); // 200
	$I->seeResponseIsJson();

	$I->seeResponseContainsJson([
		"success" => true,
		"data" => [
			"code" => 220,
		]
	]);

	$I->assertFileExists($files_root . 'folder2');

	$I->sendGet('?action=folderRename&source=test&name=folder1&path=/&newname=folder2');

	$I->seeResponseContainsJson([
		"success" => false,
		"data" => [
			"code" => 404,
		]
	]);


	$I->sendGet('?action=folderRename&source=test&name=folder2&path=/&newname=subfolder');

	$I->seeResponseContainsJson([
		"success" => false,
		"data" => [
			"code" => 400,
		]
	]);

	$I->sendGet('?action=folderRename&source=test&name=folder2&path=/&newname=folder1');


	$I->assertFileExists($files_root . 'folder1');
} finally {
	if (!realpath($files_root . 'folder1')) {
		$I->recurseCopy($files_root . 'temp', $files_root . 'folder1');
	}

	$I->recurseRemove($files_root . 'temp');
}



