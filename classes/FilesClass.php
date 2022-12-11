<?php

class FilesClass
{

    static private DatabaseClass $db;

    public function __construct(DatabaseClass $db)
    {
        self::$db = $db;
    }

    /**
     * @param string $dir
     * @param array $results
     * @return array
     */
    public function getXmlFiles(string $dir, array &$results = array()): array
    {
        $files = scandir($dir);
        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                if(pathinfo($path, PATHINFO_EXTENSION) === 'xml') {
                    if($this->allowImport($path, fileatime($path))) {
                        $results[] = ['file' => $path, 'modification_date' => fileatime($path)];
                    }
                }
            } else if ($value != "." && $value != "..") {
                $this->getXmlFiles($path, $results);
            }
        }
        return $results;
    }

    /**
     * @param string $file
     * @param int $modificationDate
     * @return bool
     */
    public function allowImport(string $file, int $modificationDate): bool
    {
        $return = true;
        if($file = self::$db->getOne('files', ['conditions' => ["file = '$file'"]])){
            if(strtotime($file[2]) >= $modificationDate){
                $return = false;
            }
        }
        return $return;
    }

    /**
     * @param array $fileData
     * @return void
     */
    public function markAsImported(array $fileData): void
    {
        if(self::$db->getOne('files', ['conditions' => ["file = '".$fileData['file']."'"]])){
            self::$db->query("UPDATE files SET import_date = NOW() WHERE file='".$fileData['file']."'");
        }
        else{
            self::$db->insert('files', ['file' => $fileData['file'], 'import_date' => date("Y-m-d H:i:s")]);
        }
    }



}