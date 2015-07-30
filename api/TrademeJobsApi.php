<?php

class TrademeJobsApi implements JobHost {
    const API_MAX_ROWS = 500;
    const API_ENDPOINT = "https://api.trademe.co.nz/v1/Search/Jobs.json";

    /** @var string */
    private $consumerKey;
    /** @var string */
    private $signature;
    /** @var array */
    private $parms = array('page' => 0, 'rows' => 0);
    /** @var int */
    private $totalCount;
    /** @var int */
    private $positionWithinPage;
    /** @var array|null */
    private $resultSet;
    /** @var int */
    private $currentPageSize;

    /**
     * (PHP 5 >= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return TrademeJob|null
     */
    public function current() {
        if(isset($this->resultSet['List']) && is_array($this->resultSet['List']) && isset($this->resultSet['List'][$this->positionWithinPage])) {
            return(new TrademeJob($this->resultSet['List'][$this->positionWithinPage]));
        }
        return(null);
    }

    /**
     * (PHP 5 >= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next() {
        $this->positionWithinPage++;
        if($this->positionWithinPage >= $this->currentPageSize) {
            $this->nextPage();
        }
    }

    /**
     * (PHP 5 >= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key() {
        $currentJob = $this->current();
        if($currentJob != null) {
            return($currentJob->getId());
        } else {
            return(null);
        }
    }

    /**
     * (PHP 5 >= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid() {
        return($this->key() !== null);
    }

    /**
     * (PHP 5 >= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind() {
        $this->setPage(1);
    }

    /**
     * @param string $key
     */
    public function setConsumerKey($key) {
        $this->consumerKey = $key;
    }

    /**
     * @param string $signature
     */
    public function setSignature($signature) {
        $this->signature = $signature;
    }

    /**
     * @return int
     */
    public function getTotalPages() {
        return((int)ceil($this->getTotalCount() / $this->getPageSize()));
    }

    /**
     * @return int
     */
    public function getTotalCount() {
        return($this->totalCount);
    }

    /**
     * @return void
     */
    public function runQuery() {
        $this->setPageSize(self::API_MAX_ROWS);
        $this->setPage(1);
    }

    /**
     * @return string
     */
    private function buildParms() {
        $parms = array();
        foreach($this->parms as $k=>$v) {
            $parms[] = $k . "=" . urlencode($v);
        }
        return(implode('&', $parms));
    }

    /**
     * @return string
     */
    private function buildAccessToken() {
        $accesstoken = array(
            'realm'=> 'https://api.trademe.co.nz/v1/Search/Jobs.json',
            'oauth_consumer_key'=> $this->consumerKey,
            'oauth_signature' => $this->signature . '&',
            'oauth_signature_method'=> 'PLAINTEXT',
            'oauth_timestamp'=> time(),
            'oauth_nonce'=> substr(uniqid(),0,6),
            'oauth_version' => '1.0'
        );
        $build = array();
        foreach($accesstoken as $k=>$v) {
            $build[] = $k . '="' . urlencode($v) . '"';
        }
        return(implode(',', $build));
    }

    /**
     * Make the call to the api return the result
     * @return null|mixed
     */
    private function callApi() {
        $curl = curl_init(self::API_ENDPOINT . '?' . $this->buildParms());
        $header = array('Authorization: OAuth ' . $this->buildAccessToken());
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $resp = curl_exec($curl);
        curl_close($curl);
        $this->resultSet = json_decode($resp, true);
        if(isset($this->resultSet['TotalCount'])) {
            $this->totalCount = (int)$this->resultSet['TotalCount'];
        }
        if(isset($this->resultSet['Page'])) {
            $this->parms['page'] = (int)$this->resultSet['Page'];
        }
        if(isset($this->resultSet['PageSize'])) {
            $this->currentPageSize = (int)$this->resultSet['PageSize'];
        }
        return($this->resultSet);
    }

    /**
     * @param int $rows
     * @return int - The number of rows
     */
    private function setPageSize($rows) {
        if(!isset($this->parms)) {
            $this->parms = array();
        }
        $this->parms['rows'] = (int)$rows;
        return($rows);
    }

    /**
     * @param int $page
     * @return int
     */
    private function setPage($page) {
        if((int)$this->parms['page'] != (int)$page) {
            $this->parms['page'] = (int)$page;
            $this->callApi();
        }
        $this->positionWithinPage = 0;
        return($this->getCurrentIndex());
    }

    /**
     * Increment the page counter and make a call to the api, return the current index
     * @return int
     */
    private function nextPage() {
        $this->positionWithinPage = 0;
        $this->setPage($this->getCurrentPage() + 1);
        return($this->getCurrentIndex());
    }

    /**
     * @return int
     */
    private function getCurrentIndex() {
        return((int)(($this->getCurrentPage() * $this->getPageSize()) + $this->positionWithinPage));
    }

    /**
     * @return int
     */
    private function getPageSize() {
        return((int)($this->parms['rows']));
    }

    /**
     * @return int
     */
    private function getCurrentPage() {
        if(!isset($this->parms['page'])) {
            $this->parms['page'] = 0;
        }
        return((int)$this->parms['page']);
    }
}

