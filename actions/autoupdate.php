<?php
if (!defined("WIKINI_VERSION")) {
    die("acc&egrave;s direct interdit");
}

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

if (!isset($this->config['wikini_repository'])) {
    $this->config['wikini_repository'] = "http://yeswiki.net/repository/";
}
$handle = fopen($this->config['wikini_repository'] . "repo.txt", 'r');
$data = "";
while (!feof($handle)) {
    $data .= fread($handle, 10);
}
fclose($handle);

//TODO : Comparer la version de YesWiki avec celle du dépot et changer la
//couleur du bouton en fonction
$color = "btn-warning";

if ($this->UserIsAdmin()) {
    echo " " . $GLOBALS['wiki']->Format(
        "{{button link=\"http://google.fr\" text=\"update\" class=\"$color\"}}"
    );
}
