<?php

namespace TutorHub;


class ImageUpload
{
    /**
     * No error.
     */
    const ERROR_OK = 0;
    /**
     * Invalid file type.
     */
    const ERROR_TYPE = 1;
    /**
     * Maximum size exceeded.
     */
    const ERROR_SIZE = 2;
    /**
     * Maximum dimensions exceeded.
     */
    const ERROR_DIMS = 3;
    /**
     * An unspecified error occurred.
     */
    const ERROR_GENERIC = 4;

    /**
     * Uploads a file.
     *
     * @param array $file File from $_FILES.
     * @return array An array with 'error' => ERROR_*.
     *               If ERROR_OK, 'fname' => uploaded file name.
     */
    public static function upload(array $file) : array
    {
        if (!isset($file['error']) || is_array($file['error']))
            return ['error' => self::ERROR_GENERIC];

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return ['error' => self::ERROR_SIZE];
            case UPLOAD_ERR_NO_FILE:
            default:
                return ['error' => self::ERROR_GENERIC];
        }

        $conf = Config::get();

        if ($file['size'] > $conf['upload_maxsize'])
            return ['error' => self::ERROR_SIZE];

        $mime = mime_content_type($file['tmp_name']);
        $exts = [
            'image/gif' => 'gif',
            'image/jpeg' => 'jpg',
            'image/png' => 'png'
        ];
        if (!isset($exts[$mime]))
            return ['error' => self::ERROR_TYPE];
        $ext = $exts[$mime];

        // MD4/5 is broken
        // SHA1 has a chosen-prefix collision attack (cf. SHAttered)
        // So we use SHA256
        $hash = hash_file('sha256', $file['tmp_name']);

        $fname = "$hash.$ext";
        $path = $conf['upload_root'] . '/' . $fname;
        if (!is_file($path) && !move_uploaded_file($file['tmp_name'], $path))
            return ['error' => self::ERROR_GENERIC];

        return [
            'error' => self::ERROR_OK,
            'fname' => $fname
        ];
    }
}
