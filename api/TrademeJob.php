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
    /** @var array */
    private static $locationCache = array();

    /** @var  array */
    private $dataset;

    public function __construct(array $dataset) {
        $this->dataset = $dataset;
        if(self::$daoJobs === null) {
            self::$daoJobs = new DAO('jobs');
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
     * @return array
     */
    public function getDataset() {
        $build = array(
            'jobId' => $this->getId(),
            'locationId' => $this->getLocationId()
        );
        return($build);
    }
}