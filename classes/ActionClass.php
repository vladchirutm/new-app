<?php

require_once('classes/DatabaseClass.php');
require_once('classes/FilesClass.php');

class ActionClass
{
    static private DatabaseClass $db;

    public function __construct()
    {
        self::$db = new DatabaseClass(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD, DB_PORT);
    }

    public static function importBooks(): void
    {

        $f = new FilesClass(self::$db);

        $files = $f->getXmlFiles(IMPORT_DIR);

        foreach ($files as $file){

            $books = new SimpleXMLElement(file_get_contents($file['file']));

            foreach ($books->book as $book) {

                $name = sanitizeItem((string) $book->author, true);
                $title = sanitizeItem((string) $book->name, true);

                if (!($authorRecord = self::$db->getOne('authors', ['conditions' => ["name = '" . $name . "'"], 'fields' => ['author_id', 'name']]))) {
                    $authorRecord = self::$db->insert('authors', ['name' => $name], 'author_id');
                }
                if (!self::$db->getOne('books', ['conditions' => ["author_id = '$authorRecord[0]'", "title = '$title'"]])) {
                    self::$db->insert('books', ['author_id' => $authorRecord[0], 'title' => $title]);
                }
            }

            $f->markAsImported($file);
        }
    }

    /**
     * @param string $term
     * @return bool|array
     */
    public static function searchForAuthors(string $term): bool|array
    {
        return self::$db->get('authors',
            [
                'conditions' => ["LOWER(name) like '%" . strtolower(sanitizeItem($term, 'string', true)) . "%'"],
                'joins' => [
                        [
                        'table' => 'books',
                        'type' => 'left join',
                        'conditions' => [
                            'authors.author_id = books.author_id'
                        ]
                    ]
                ]
            ]);

    }

}