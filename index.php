<?php
require_once __DIR__.'/src/PHPTelebot.php';
require_once __DIR__.'/src/xc.php';

// Read token & username
function readToken($input){
    $TOKENr = file_get_contents("Xppai.WRT");
    $raw = explode("\n",$TOKENr);
    $TOKEN = $raw[0];
    $USERNAME = $raw[1];
    if ($input == "token") {
        return $TOKEN;
    }elseif($input == "username"){
        return $USERNAME;
    }
}

$bot = new PHPTelebot(readToken("token"), readToken("username"));

// Ping Command
$bot->cmd('/ping','yes');

// start cmd & cmd list
$bot->cmd('/start',"Welcome to RumiWRT\n/cmdlist to see all comand");
$bot->cmd('/cmdlist', function () {
    $options = ['parse_mode' => 'html','reply' => true];
    return Bot::sendMessage("
📁Aria2 Command
 <code>/aria2add</code> | Add task
 <code>/aria2stats</code> | Aria2 status
 <code>/aria2pause</code> | Pause all
 <code>/aria2resume</code> | Resume all
📁OpenClash Command
 <code>/oc</code> | OC Information
 <code>/proxies</code> | Proxies status 
 <code>/rules</code> | Rule list 
📁MyXL Command 
 <code>/myxl</code> | Bandwidth usage 
 <code>/setxl 087</code> | Set default number
📁System Information
 <code>/vnstat</code> | Bandwidth usage 
 <code>/memory</code> | Memory status 
 <code>/myip</code> | Get ip details 
 <code>/speedtest</code> | Speedtest 
 <code>/ping</code> | Ping bot
 <code>/sysinfo</code> | System Information",$options);
});

// OpenWRT Command 
$bot->cmd('/proxies', function () {
    $options = ['parse_mode' => 'html','reply' => true];
    return Bot::sendMessage("<code>".Proxies()."</code>",$options);
});

$bot->cmd('/vnstat', function ($input) {
    $options = ['parse_mode' => 'html','reply' => true];
    return Bot::sendMessage("<code>".shell_exec("vnstat $input")."</code>",$options);
});

$bot->cmd('/memory', function () {
    $options = ['parse_mode' => 'html','reply' => true];
    return Bot::sendMessage("<code>".shell_exec("cat /proc/meminfo | sed -n '1,5p'")."</code>",$options);
});

$bot->cmd('/sysinfo', function () {
    $options = ['parse_mode' => 'html','reply' => true];
    return Bot::sendMessage("<code>".shell_exec("src/plugins/sysinfo.sh -bw")."</code>",$options);
});

$bot->cmd('/oc', function () {
    $options = ['parse_mode' => 'html','reply' => true];
    return Bot::sendMessage("<code>".shell_exec("src/plugins/oc.sh")."</code>",$options);
});

$bot->cmd('/myip', function () {
    $options = ['parse_mode' => 'html','reply' => true];
    return Bot::sendMessage("<code>".myip()."</code>",$options);
});

$bot->cmd('/rules', function () {
    $options = ['parse_mode' => 'html','reply' => true];
    return Bot::sendMessage("<code>".Rules()."</code>",$options);
});

$bot->cmd('/speedtest', function () {
    $options = ['parse_mode' => 'html','reply' => true];
    Bot::sendMessage("<code>Speedtest on Progress</code>", $options);
    return Bot::sendMessage("<code>".Speedtest()."</code>",$options);
});

//Myxl cmd
$bot->cmd('/setxl', function ($number) {
    $options = ['parse_mode' => 'html','reply' => true];
    if ($number == "") {
        Bot::sendMessage("<code>Masukan nomor yang mau di set sebagai default /setxl 087x</code>",$options);
    }else{
        shell_exec("echo '$number' > xl");
        Bot::sendMessage("<code>Nomer $number disetting sebagai default\nSilahkan gunakan cmd /myxl tanpa memasukkan nomor</code>",$options);
    }
});

$bot->cmd('/myxl', function ($number) {
    $options = ['parse_mode' => 'html','reply' => true];
    Bot::sendMessage("<code>MyXL on Progress</code>", $options);
    return Bot::sendMessage("<code>".MyXL($number)."</code>",$options);
});
//Myxl cmd end

//Aria2 cmd
$bot->cmd('/aria2add', function ($url) {
    $options = ['parse_mode' => 'html','reply' => true];
    return Bot::sendMessage("<code>".shell_exec("src/plugins/add.sh $url")."</code>",$options);
});

$bot->cmd('/aria2stats', function () {
    $options = ['parse_mode' => 'html','reply' => true];
    return Bot::sendMessage("<code>".shell_exec("src/plugins/stats.sh")."</code>",$options);
});

$bot->cmd('/aria2pause', function () {
    $options = ['parse_mode' => 'html','reply' => true];
    return Bot::sendMessage("<code>".shell_exec("src/plugins/pause.sh")."</code>",$options);
});

$bot->cmd('/aria2resume', function () {
    $options = ['parse_mode' => 'html','reply' => true];
    return Bot::sendMessage("<code>".shell_exec("src/plugins/resume.sh")."</code>",$options);
});
//Aria2 cmd end

//inline command
$bot->on('inline', function ($cmd,$input) {
    
    if($cmd == 'proxies'){
        $results[] = [
        'type' => 'article',
        'id' => 'unique_id1',
        'title' => Proxies(),
        'parse_mode' => 'html',
        'message_text' => "<code>".Proxies()."</code>",
        ];
    }elseif($cmd == 'rules'){
        $results[] = [
        'type' => 'article',
        'id' => 'unique_id1',
        'title' => Rules(),
        'parse_mode' => 'html',
        'message_text' => "<code>".Rules()."</code>",
        ];
    }elseif($cmd == 'myxl'){
        $results[] = [
        'type' => 'article',
        'id' => 'unique_id1',
        'title' => MyXL($input),
        'parse_mode' => 'html',
        'message_text' => "<code>".MyXL($input)."</code>",
        ];
    }
    
    $options = [
        'cache_time' => 3600,
    ];

    return Bot::answerInlineQuery($results, $options);
});

$bot->run();
