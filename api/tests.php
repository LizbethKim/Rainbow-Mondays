<?php
include('autoload.php');
class TrademeJobTest extends PHPUnit_Framework_TestCase
{
  public function testID()
  {
    $entry = json_decode('{"ListingId":929201760,"Title":"Experienced Chef Required!!!","Category":"5000-5097-5100-","StartPrice":0,"StartDate":"\/Date(1438818725497)\/","EndDate":"\/Date(1441454399000)\/","ListingLength":null,"IsFeatured":true,"AsAt":"\/Date(1438819080486)\/","CategoryPath":"\/Trade-Me-Jobs\/Hospitality-tourism\/Chefs","Region":"Canterbury","Suburb":"Christchurch City","NoteDate":"\/Date(0)\/","ReserveState":3,"IsClassified":true,"PriceDisplay":"","District":"Christchurch City","JobType":"PT","PayBenefits":null,"Reference":"Beach Cafe","ApplicationDetails":null,"IsWorkPermitRequired":false,"Instructions":"Email CV :)","Listed":null,"Keywords":null,"JobCategory":null,"JobSubcategory":null,"Company":"Beach Cafe","JobLocation":"Canterbury,Christchurch City","ContractLength":"PER","PayType":"Salary","JobPackId":null,"Body":"Beach Cafe Experienced Chef Required!!!","Agency":null,"JobApplicationDetails":{"OnlineApplicationType":1,"ContactName":"033828599","ApplyViaTradeMe":"http:\/\/www.trademe.co.nz\/Browse\/Jobs\/ApplyOnline.aspx?mode=apply_online&referenceId=929201760&sellerId=2931451"}}', true);
    $a = new TrademeJob($entry);
    $this->assertEquals(929201760, $a->getId());
  }
}
