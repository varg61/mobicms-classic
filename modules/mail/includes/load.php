<?php
/**
 * mobiCMS (https://mobicms.org/)
 * This file is part of mobiCMS Content Management System.
 *
 * @license     https://opensource.org/licenses/GPL-3.0 GPL-3.0 (see the LICENSE.md file)
 * @link        http://mobicms.org mobiCMS Project
 * @copyright   Copyright (C) mobiCMS Community
 */

defined('MOBICMS') or die('Error: restricted access');

$textl = _t('Mail');
require ROOT_PATH . 'system/head.php';

/** @var Psr\Container\ContainerInterface $container */
$container = App::getContainer();

/** @var PDO $db */
$db = $container->get(PDO::class);

/** @var Mobicms\Api\UserInterface $systemUser */
$systemUser = $container->get(Mobicms\Api\UserInterface::class);

/** @var Mobicms\Api\ToolsInterface $tools */
$tools = $container->get(Mobicms\Api\ToolsInterface::class);

if ($id) {
    $req = $db->query("SELECT * FROM `cms_mail` WHERE (`user_id`='" . $systemUser->id . "' OR `from_id`='" . $systemUser->id . "') AND `id` = '$id' AND `file_name` != '' AND `delete`!='" . $systemUser->id . "' LIMIT 1");

    if (!$req->rowCount()) {
        //Выводим ошибку
        echo $tools->displayError(_t('Such file does not exist'));
        require ROOT_PATH . 'system/end.php';
        exit;
    }

    $res = $req->fetch();

    if (file_exists(ROOT_PATH . 'files/mail/' . $res['file_name'])) {
        $db->exec("UPDATE `cms_mail` SET `count` = `count`+1 WHERE `id` = '$id' LIMIT 1");
        header('Location: ../files/mail/' . $res['file_name']);
        exit;
    } else {
        echo $tools->displayError(_t('Such file does not exist'));
    }
} else {
    echo $tools->displayError(_t('No file selected'));
}