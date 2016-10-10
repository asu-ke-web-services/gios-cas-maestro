<?php // this is sourced from the GIOS API
// This is a static class for interacting with the ASU public directory
class ASUDirectory {
  // to find out about a person given an asurite:
  //   https://webapp4.asu.edu/directory/ws/search?asuriteId=xxxxxxx
  // or we could in the future by first+last name:
  //   https://webapp4.asu.edu/directory/ws/search?q=FIRST+LAST

 static public function get_directory_info_by_asurite($asurite) {
    if($asurite == NULL || strlen($asurite) < 3 || strlen($asurite) > 12) { return NULL; }
    $asurite = urlencode(strtolower($asurite));
    $xml = file_get_contents("https://webapp4.asu.edu/directory/ws/search?asuriteId=".$asurite);
    if(empty($xml)) return NULL; // could get nothing back from the server
    $feed = new SimpleXMLElement($xml);
    if(empty($feed)) return NULL; // could get an empty result set from the server
    return $feed;
  }

  /** get_display_name_from_directory_info
   * @return the displayname from the xml directory info or return empty string
   */
  static public function  get_display_name_from_directory_info($xml) {
    if(isset($xml->person) && isset($xml->person->displayName)) {
      return strval($xml->person->displayName);
    }
    return "";
  }

  /** get_last_name_from_directory_info
   * @return the lastName from the xml directory info or return empty string
   */
  static public function  get_last_name_from_directory_info($xml) {
    if(isset($xml->person) && isset($xml->person->lastName)) {
      return strval($xml->person->lastName);
    }
    return "";
  }

  /** get_first_name_from_directory_info
   * @return the firstName from the xml directory info or return empty string
   */
  static public function  get_first_name_from_directory_info($xml) {
    if(isset($xml->person) && isset($xml->person->firstName)) {
      return strval($xml->person->firstName);
    }
    return "";
  }

  /** get_email_from_directory_info
   * @return the email from the xml directory info or return empty string
   */
  static public function  get_email_from_directory_info($xml) {
    if(isset($xml->person) && isset($xml->person->email)) {
      return strval($xml->person->email);
    }
    return "";
  }


  /**  has_SOS_plan_from_directory_info
  The complexity of this function is because of how the structure changes for when there
    are multiple plans:

    eg: one plan
        [plans] => SimpleXMLElement Object
                (
                    [plan] => SimpleXMLElement Object
                        (
                            [acadPlan] => LAEESBA
                            [acadCareer] => UGRD
                              ....
                        )
                )
    eg: more than one plan
      [plans] => SimpleXMLElement Object
                (
                    [plan] => Array
                        (
                            [0] => SimpleXMLElement Object
                                (
                                    [acadPlan] => LASESGSBS
                                    [acadCareer] => UGRD
                                     ....
                                )
                            [1] => SimpleXMLElement Object
                                (
                                    [acadPlan] => SUSUSTBS
                                    [acadCareer] => UGRD
                                    [acadCareerDescr] => Undergraduate
                                    [acadPlanType] => MAJ
                                    [acadProg] => UGSU
                                    [acadPlanDescr] => Sustainability
                                    [acadProgDescr] => School of Sustainability
                                    [progStatus] => AC
                                )
                        )
                )
    @returns true if the person has an SOS plan, or false if not
   */
  static public function  has_SOS_plan_from_directory_info($xml) {
    if(isset($xml->person) && isset($xml->person->plans)) {
      foreach($xml->person->plans->plan as $plan) {
        // print_r($plan->plan);
        if(isset($plan) && is_array($plan)) {
          // echo "more than one plan! ";
          // print_r($plan);
          // they have more than one plan:
          foreach($plan as $sub_plan) {
            if(isset($sub_plan->acadPlanDescr) && stristr($sub_plan->acadPlanDescr, "Sustainability")) {
              return TRUE;
            }
          }
        } else if(isset($plan->acadPlanDescr) && stristr($plan->acadPlanDescr, "Sustainability")) {
          return TRUE;
        } else {
          // echo "there is a plan but its not sustainability, its ".$plan->acadPlanDescr."\n";
        }
      }
    }
    // echo "NO PLANS\n";
    return FALSE;
  }

}
