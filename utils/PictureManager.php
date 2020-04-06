<?php


namespace photobox\utils;


class PictureManager
{

    public static function storeEventPicture($id_event, $picture)
    {
        $rawdata = $picture;
        $rawdata = explode(',', $rawdata);
        $picturecontent = base64_decode($rawdata[1]);
        $basename = bin2hex(random_bytes(16));
        if (!is_dir("../uploads/" . $id_event)) {
            mkdir("../uploads/" . $id_event);
        }
        $path = "../uploads/" . $id_event . '/' . $basename;
        file_put_contents($path, $picturecontent);

        return $basename;
    }
}