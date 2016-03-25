<?php
namespace AutoUpdate;

abstract class Package
{
    const PREFIX_FILENAME = 'yeswiki_';

    // URL vers le fichier dans le dépôt.
    protected $address;
    // Chemin vers le dossier temporaire ou est décompressé le paquet
    protected $tmpPath = null;
    // Chemin vers le paquet temporaire téléchargé localement
    protected $tmpFile = null;
    // nom du tool
    public $name = "";
    // Version du paquet
    protected $version;

    abstract public function upgrade($desPath);
    abstract protected function name();

    public function __construct($version, $address)
    {
        $this->version = new Version($version);
        $this->address = $address;
        $this->name = $this->name();
    }

    public function checkIntegrity()
    {
        if ($this->tmpFile === null) {
            throw new \Exception("Le paquet n'a pas été téléchargé.", 1);
        }
        $md5Repo = $this->getMD5();
        $md5File = md5_file($this->tmpFile);
        return ($md5File === $md5Repo);
    }

    public function getFile()
    {
        $this->downloadFile($this->address);

        if (is_file($this->tmpFile)) {
            return $this->tmpFile;
        }
        $this->tmpFile = null;
        return false;
    }

    public function version()
    {
        return $this->version;
    }

    public function extract()
    {
        if ($this->tmpFile === null) {
            throw new \Exception("Le paquet n'a pas été téléchargé.", 1);
        }

        $zip = new \ZipArchive;
        if (true !== $zip->open($this->tmpFile)) {
            return false;
        }

        $files = new Files();
        $this->tmpPath = $files->tmpdir();
        if (true !== $zip->extractTo($this->tmpPath)) {
            return false;
        }

        $zip->close();

        return $files->tmpdir();
    }

    private function getMD5()
    {
        $disMd5File = file_get_contents($this->address . '.md5');
        return explode('  ', $disMd5File)[0];
    }

    private function downloadFile($sourceUrl)
    {
        $this->tmpFile = tempnam(sys_get_temp_dir(), $this::PREFIX_FILENAME);
        file_put_contents($this->tmpFile, fopen($sourceUrl, 'r'));
    }
}
