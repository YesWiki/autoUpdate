<?php
if (!defined("WIKINI_VERSION")) {
    die("acc&egrave;s direct interdit");
}

$AU_PATH_NAME = "tools/yeswiki-tool-autoupdate";

if (!class_exists('attach')) {
    include 'tools/attach/actions/attach.class.php';
}

print("Version de YesWiki : ");
unset($this->config['wikini_version']);

if (isset($this->config['wikini_version'])) {
    print($this->config['wikini_version']);
} else {
    print(_t('AU_UNKNOW'));
}

// TODO : Récupérer les infos du repository et les comparer aux infos locales
/*if (!isset($this->config['wikini_repository'])) {
$this->config['wikini_repository'] = "http://yeswiki.net/repository/";
}
$handle = fopen($this->config['wikini_repository'] . "repo.txt", 'r');
$data = "";
while (!feof($handle)) {
$data .= fread($handle, 10);
}
fclose($handle);
 */

//TODO : Comparer la version de YesWiki avec celle du dépot et changer la
//couleur du bouton en fonction

$color = "btn-warning";

$link = substr($this->config['base_url'], 0, -15) . $AU_PATH_NAME;

if ($this->UserIsAdmin()) {
    echo " " . $GLOBALS['wiki']->Format(
        "{{button link=\"$link\" text=\"update\" class=\"$color\"}}"
    );
}
