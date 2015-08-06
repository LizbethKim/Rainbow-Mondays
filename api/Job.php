<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 30/07/15
 * Time: 2:18 PM
 */
interface Job {
    public function __construct(array $dataset);
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getLocation();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return array
     */
    public function getDataset();

    /**
     * @return string
     *
     */
    public function getCategory();
}