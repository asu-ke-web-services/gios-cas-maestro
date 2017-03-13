<?php
// This is a static class for interacting with the ASU iSearch service
class ASUiSearch {
  // To display iSearch profile, you need their eid (Employee ID?)
  // https://isearch.asu.edu/profile/{eid}

  // Profile data also available via xml and JSON:
  // to find out about a person given an asurite:
  //   https://asudir-solr.asu.edu/asudir/directory/select?q=asuriteId:{asurite}&wt=json
  //   https://asudir-solr.asu.edu/asudir/directory/select?q=asuriteId:{asurite}&wt=xml


  static public function get_isearch_info_by_asurite($asurite) {
    if ( $asurite == NULL || strlen( $asurite ) < 3 || strlen( $asurite ) > 12 ) {
      return NULL;
    }
    $asurite = urlencode( $asurite );
    $json = file_get_contents( "https://asudir-solr.asu.edu/asudir/directory/select?q=asuriteId:" . $asurite . "&wt=json" );
    if ( empty( $json ) ) {
      return NULL;
    }
    $info = json_decode ( $json, true );
    if ( 0 == $info['response']['numFound'] ) {
      return NULL;
    }
    return $info;
  }

  static public function get_eid_from_isearch_info( $info ) {
    if ( isset( $info['response']['docs'][0]['eid'] ) ) {
      return intval( $info['response']['docs'][0]['eid'] );
    }
    return "";
  }

  static public function get_asurite_from_isearch_info( $info ) {
    if ( isset( $info['response']['docs'][0]['asuriteId'] ) ) {
      return strval( $info['response']['docs'][0]['asuriteId'] );
    }
    return "";
  }

  static public function get_display_name_from_isearch_info( $info ) {
    if ( isset( $info['response']['docs'][0]['displayName'] ) ) {
      return strval( $info['response']['docs'][0]['displayName'] );
    }
    return "";
  }

  static public function get_first_name_from_isearch_info( $info ) {
    if ( isset( $info['response']['docs'][0]['firstName'] ) ) {
      return strval( $info['response']['docs'][0]['firstName'] );
    }
    return "";
  }

  static public function get_last_name_from_isearch_info( $info ) {
    if ( isset( $info['response']['docs'][0]['lastName'] ) ) {
      return strval( $info['response']['docs'][0]['lastName'] );
    }
    return "";
  }

  static public function get_email_from_isearch_info( $info ) {
    if ( isset( $info['response']['docs'][0]['emailAddress'] ) ) {
      return strval( $info['response']['docs'][0]['emailAddress'] );
    }
    return "";
  }

  static public function get_user_type_from_isearch_info( $info ) {
    $student = FALSE;
    $faculty = FALSE;
    $staff = FALSE;
    if ( isset( $info['response']['docs'][0]['affiliations'] ) ) {
      foreach ( $info['response']['docs'][0]['affiliations'] as $affiliation ) {
        if ( 'Student' == $affiliation ) {
          $student = TRUE;
        }
        if ( 'Employee' == $affiliation ) {
          foreach ( $info['response']['docs'][0]['emplClasses'] as $employee_class ) {
            if ( 'Faculty' == $employee_class ) {
              $faculty = TRUE;
            } elseif ( 'University Staff' == $employee_class ) {
              $staff = TRUE;
            }
          }
        }
      }
    }
    // in case, user has multiple classicifcations (staff enrolled as student)
    // role precedence: student > faculty > staff
    if ( $student ) {
      return 'student';
    } elseif ( $faculty ) {
      return 'faculty';
    } elseif ( $staff ) {
      return 'staff';
    } else {
      return FALSE;
    }
  }

  static public function is_student_from_isearch_info( $info ) {
    if ( isset( $info['response']['docs'][0]['affiliations'] ) ) {
      foreach ( $info['response']['docs'][0]['affiliations'] as $affiliation ) {
        if ( 'Student' == $affiliation ) {
          return TRUE;
        }
      }
    }
    return FALSE;
  }

  static public function is_faculty_from_isearch_info( $info ) {
    if ( isset( $info['response']['docs'][0]['affiliations'] ) ) {
      foreach ( $info['response']['docs'][0]['affiliations'] as $affiliation ) {
        if ( 'Employee' == $affiliation ) {
          foreach ( $info['response']['docs'][0]['emplClasses'] as $employee_class ) {
            if ( 'Faculty' == $employee_class ) {
              return TRUE;
            }
          }
        }
      }
    }
    return FALSE;
  }

  static public function is_staff_from_isearch_info( $info ) {
    if ( isset( $info['response']['docs'][0]['affiliations'] ) ) {
      foreach ( $info['response']['docs'][0]['affiliations'] as $affiliation ) {
        if ( 'Employee' == $affiliation ) {
          foreach ( $info['response']['docs'][0]['emplClasses'] as $employee_class ) {
            if ( 'University Staff' == $employee_class ) {
              return TRUE;
            }
          }
        }
      }
    }
    return FALSE;
  }


  static public function  has_SOS_plan_from_isearch_info( $info ) {
    if ( $info['response']['numFound'] > 0 ) {
      if ( $info['response']['docs'][0]['programs'] ) {
        foreach ( $info['response']['docs'][0]['programs'] as $program ) {
          // look for SOS program
          if ( 'School of Sustainability' == $program ) {
            foreach ( $info['response']['docs'][0]['majors'] as $major ) {
              // is student majoring in Sustainability
              if ( 'Sustainability' == $major ) {
                return TRUE;
              }
            }
          }
        }
      }
    }

    return FALSE;
  }
}
