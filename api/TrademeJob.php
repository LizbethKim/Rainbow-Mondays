<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 30/07/15
 * Time: 2:19 PM
 */
class TrademeJob implements Job {
    /** @var DAO */
    private static $daoJobs;
    /** @var DAO */
    private static $daoCategories;
    /** @var array */
    private static $locationCache = array();
    /** @var  array */
    private $dataset;

    public function __construct(array $dataset) {
        $this->dataset = $dataset;
        if(self::$daoJobs === null) {
            self::$daoJobs = new DAO('jobs');
        }
        if(self::$daoCategories === null) {
            self::$daoCategories = new DAO('categories');
        }
    }

    /**
     * @return int
     */
    public function getId() {
        return((int)$this->dataset['ListingId']);
    }

    /**
     * @return string
     */
    public function getLocation() {
        return((string)$this->dataset['Suburb']);
    }

    /**
     * @return string
     */
    public function getTitle() {
        return($this->dataset['Title']);
    }

    /**
     * @return int|null
     */
    public function getLocationId() {
        if(isset(self::$locationCache[$this->getLocation()])) {
            if(self::$locationCache[$this->getLocation()] !== null) {
                return(self::$locationCache[$this->getLocation()]['id']);
            } else {
                return(null);
            }
        } else {
            $dao = self::$daoJobs;
            $result = $dao->query("SELECT * FROM districts WHERE name = '" . $dao->escape($this->getLocation()) . "';");
            if(count($result) > 0) {
                self::$locationCache[$this->getLocation()] = $result[0];
            } else {
                self::$locationCache[$this->getLocation()] = null;
            }
            return($this->getLocationId());
        }
    }

    /**
     * Retrieve the job category id from the category string.
     * If it is not in the database, return
     * @param int $parentLevel
     * @return int
     * @throws Exception
     */
    private function getCategoryId($parentLevel = 0) {
        $categories = explode('-', $this->dataset['Category']);
        $offset = count($categories) - 2 - $parentLevel;
        if($offset < 0) {
            throw new Exception("Could not find category");
        }
        return((int) $categories[$offset]);

    }

    /**
    * @return string
    */
    private function getType(){
      if ($this->dataset['JobType'] == 'FT') return 0;
      if ($this->dataset['JobType'] == 'PT') return 1;
      return 2;
    }

    /**
     * @return int
     */
    public function getListedTime() {
        $time = $this->dataset['StartDate'];
        return((int)((float)preg_replace('/[^0-9]/', '', $time) / 1000.0));
    }
    /**
     * @return array
     */
    public function getDataset() {
        $build = array(
            'id' => $this->getId(),
            'jobTitle' => $this->getTitle(),
            'locationId' => $this->getLocationId(),
            'categoryId' => $this->getCategoryId(),
            'listedTime' => (int)$this->getListedTime(),
            'type' => $this->getType()
        );
        return($build);
    }

    /**
     * Get the textual name of a category
     * @return string
     */
    public function getCategory() {
        $result = self::$daoCategories->query("select categoryName from categories where id = " . $this->getCategoryId());
        if(is_array($result) && count($result) > 0) {
            return($result[0]['categoryName']);
        } else {
            return('Unknown Category');
        }
    }
}
