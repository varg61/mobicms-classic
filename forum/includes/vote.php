<?php

defined('MOBICMS') or die('Error: restricted access');

/** @var Psr\Container\ContainerInterface $container */
$container = App::getContainer();

/** @var Mobicms\Api\UserInterface $systemUser */
$systemUser = $container->get(Mobicms\Api\UserInterface::class);

if ($systemUser->isValid()) {
    /** @var PDO $db */
    $db = $container->get(PDO::class);

    /** @var Mobicms\Api\ToolsInterface $tools */
    $tools = $container->get(Mobicms\Api\ToolsInterface::class);

    $topic = $db->query("SELECT COUNT(*) FROM `forum` WHERE `type`='t' AND `id` = '$id' AND `edit` != '1'")->fetchColumn();
    $vote = abs(intval($_POST['vote']));
    $topic_vote = $db->query("SELECT COUNT(*) FROM `cms_forum_vote` WHERE `type` = '2' AND `id` = '$vote' AND `topic` = '$id'")->fetchColumn();
    $vote_user = $db->query("SELECT COUNT(*) FROM `cms_forum_vote_users` WHERE `user` = '" . $systemUser->id . "' AND `topic` = '$id'")->fetchColumn();
    require('../system/head.php');

    if ($topic_vote == 0 || $vote_user > 0 || $topic == 0) {
        echo $tools->displayError(_t('Wrong data'));
        require('../system/end.php');
        exit;
    }

    $db->exec("INSERT INTO `cms_forum_vote_users` SET `topic` = '$id', `user` = '" . $systemUser->id . "', `vote` = '$vote'");
    $db->exec("UPDATE `cms_forum_vote` SET `count` = count + 1 WHERE id = '$vote'");
    $db->exec("UPDATE `cms_forum_vote` SET `count` = count + 1 WHERE topic = '$id' AND `type` = '1'");
    echo _t('Vote accepted') . '<br /><a href="' . htmlspecialchars(getenv("HTTP_REFERER")) . '">' . _t('Back') . '</a>';
}
