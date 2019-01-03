<?php

/*global $Queue, $Text, $Event*/;

leave("请到 http://blbot.ml:8080/feedback 进行反馈，谢谢！");

/*
requireSeniorAdmin();

$length = strpos($Text, "\r");
if(false===$length)$length=strlen($Text);
$title = substr($Text, 0, $length);
$body = substr($Text, $length+2);

$Queue[]= sendBack("本命令是用于报告 bug 或者提建议的，不规范的使用大概率会被拉黑！");

if($title == ''){
    $Queue[]= sendBack("参数错误，请阅读以下内容！");
    loadModule('help.issue');
    leave();
}

$result = file_get_contents();



//if(coolDown("issue/{$Event['user_id']}")<0)leave('本命令每小时只能使用一次！');
//coolDown("issue/{$Event['user_id']}", 60*60*1);
//到时候把上面的 requireSeniorAdmin() 删了再解除注释

$oauth_params = array(
    'oauth_consumer_key'      => config('bitbucket_client_key'),
    'oauth_consumer_secret'   => config('bitbucket_client_secret')
);

$user = new Bitbucket\API\User;
$user->getClient()->addListener(
    new Bitbucket\API\Http\Listener\OAuthListener($oauth_params)
);
/*

$response = $user->get();
$issue = new \Bitbucket\API\Repositories\Issues();

$issue->getClient()->addListener(
    new \Bitbucket\API\Http\Listener\OAuth2Listener($oauth_params)
);
$reply = $issue->create("BobLiu0518", "BL1040Bot", array(
    'title'     => $title,
    'content'   => '创建者：'.$Event['user_id'].'\n\n'.$body,
    'kind'      => 'bug',
    'priority'  => 'blocker'
));
$Queue[]= sendBack($reply);

$github = new Github\Client($builder, 'machine-man-preview');
$jwt = (new Builder)
    ->setIssuer(config('Github_Integration_ID'))
    ->setIssuedAt(time())
    ->setExpiration(time() + 60)
    ->sign(new Sha256(),  new Key(getData('kjBot-Github.pem')))
    ->getToken();

$github->authenticate($jwt, null, Github\Client::AUTH_JWT);
$token = $github->api('apps')->createInstallationToken(config('Github_Installation_ID'));
$github->authenticate($token['token'], null, Github\Client::AUTH_HTTP_TOKEN);

$result = $github->api('issue')->create('kj415j45', 'kjBot', [
    'title' => $title,
    'body' => '>创建者：'.$Event['user_id']."\n\n".$body,
    'assignees' => ['kj415j45'],
]);

$Queue[]= sendBack('Issue 创建成功！'.$result['html_url']);
$Queue[]= sendMaster($Event['user_id'].' 创建了新 issue '.$result['html_url']."\n".var_export($Event, true));

*/

?>