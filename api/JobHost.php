<?php

interface JobHost extends Iterator {
    /**
     * @param string $key
     * @return void
     */
    public function setConsumerKey($key);

    /**
     * @param string $signature
     * @return void
     */
    public function setSignature($signature);

    /**
     * @return int
     */
    public function getTotalPages();

    /**
     * @return int
     */
    public function getTotalCount();

    /**
     * @return void
     */
    public function runQuery();


}