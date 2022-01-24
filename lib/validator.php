<?php
/**
 * Validation functions
 * - Provides a full range of validation rules for Cake (http://sputnik.pl/cake)
 *
 * + Supports multiple validation rules per field
 * + Supports data filters (such as 'trim')
 * + Provides default error messages for rules
 * + Supports custom error messages per rule per field
 * + Provides ability to register custom validation
 *
 * @author  Chris Scharf <scharfie@gmail.com>
 * @url     http://www.bluemargin.com/validator/
 * @version 0.7.1 6/30/2005
 */

// validator definitions

if (defined('VALID_NOT_EMPTY')) {
    trigger_error('Cake\'s validation model, which is incompatible with Validator, was found to be loaded (most likely from /libs/model.php).', E_USER_ERROR);
} // end if defined

define('VALID_NOT_EMPTY',        'VALID_NOT_EMPTY');
define('VALID_COMPARE',          'VALID_COMPARE');
define('VALID_EMAIL',            'VALID_EMAIL');
define('VALID_NUMBER',           'VALID_NUMBER');
define('VALID_RANGE',            'VALID_RANGE');
define('VALID_RANGE_LENGTH',     'VALID_RANGE_LENGTH');
define('VALID_LETTERS_ONLY',     'VALID_LETTERS_ONLY');
define('VALID_NO_PUNCTUATION',   'VALID_NO_PUNCTUATION');
define('VALID_ALPHANUMERIC',     'VALID_ALPHANUMERIC');
define('VALID_NONZERO',          'VALID_NONZERO');
define('ALL_ELEMENTS',           -1);
define('VALID_PHONE_NUMBER',	 'VALID_PHONE_NUMBER');
define('VALID_SHORT_DATE',		 'VALID_SHORT_DATE');

class Validator {
  var $validate=null;
  var $_validation_errors=array();
  var $_required_fields=null;
  var $obj;
  var $data=null;

  /**
   * Validator constructor
   * - Designed for Cake, but can be used separately (though not thoroughly tested)
   *
   *   Cake:  pass instance of an AppController as the first argument:
   *           class MyController extends AppController {
   *             function index()
   *             {
   *                 $this->validate = array(...);
   *                 $this->validator = new Validator(&$this);
   *
   *                 if ($this->validator->validates()) {
   *                    ...
   *                 }
   *             } // end index()
   *           } // end class
   *
   *   Standalone:
   *
   *           $validator = new Validator();
   *           $validator->validate = array(...);
   *
   *           if ($validator->validates()) {
   *               ...
   *           }
   *
   * @param obj   - mixed object having validate[] and params['data']
   * @param rules - mixed array of validation rules (validate[])
   * @param data  - mixed array of data (Cake params['data'])
   *
   * @author Chris Scharf <scharfie@gmail.com>
   */
  function Validator($obj=null, $rules=null, $data=null)
  {
                  //   '/^([a-z0-9][a-z0-9_\-\.\+]*)@([a-z0-9][a-z0-9\.\-]{0,63}\.[a-z]{2,3})$/i'                              );
      $email_pattern = '/^((\"[^\"\f\n\r\t\v\b]+\")|([\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/';
      $this->registerValidator(VALID_NOT_EMPTY,      'regex',    '/.+/',                                         'This field must not be empty'                          );
      $this->registerValidator(VALID_NO_PUNCTUATION, 'regex',     '/^[^().\/\*\^\?#!@$%+=,\"\'><~\[\]{}]+$/',    'This field must not contain any punctuation characters');
      $this->registerValidator(VALID_NONZERO,        'regex',    '/^-?[1-9][0-9]*/',                             'This field must be non-zero'                           );
      $this->registerValidator(VALID_ALPHANUMERIC,   'regex',    '/^[a-zA-Z0-9]+$/',                             'This field must consist only of letters and/or numbers');
      $this->registerValidator(VALID_LETTERS_ONLY,   'regex',    '/^[a-zA-Z]+$/',                                'This field must consist only of letters'               );
      $this->registerValidator(VALID_NUMBER,         'regex',    '/(^-?\d\d*\.\d*$)|(^-?\d\d*$)|(^-?\.\d\d*$)/', 'This field must be a number'                           );
      $this->registerValidator(VALID_COMPARE,        'internal', 'validatorValidCompare',                        'The fields do not match'                               );
      $this->registerValidator(VALID_RANGE,          'internal', 'validatorValidRange',                          'The value is out of range'                             );
      $this->registerValidator(VALID_RANGE_LENGTH,   'internal', 'validatorValidRangeLength',                    'The length of the value is out of range'               );
      $this->registerValidator(VALID_EMAIL,          'regex',    $email_pattern,                                 'This field must contain a valid e-mail address'		 );
      $this->registerValidator(VALID_PHONE_NUMBER, 	 'regex',	 '/^\(\d{3}\)\s?\d{3}\-\d{4}$/',				 'Not a valid phone format.'							 );
	  $this->registerValidator(VALID_SHORT_DATE,	 'regex',	 '/^\d{1,2}\/\d{1,2}\/\d{4}$/',					 'Not a valid date (i.e. mm/dd/yyyy)'					 );
      $this->_init(&$obj, &$rules, &$data);
  }

  /**
   * Obtains a reference to the data
   *
   *
   * @author Chris Scharf <scharfie@gmail.com>
   * @version 0.1 5/17/2005
   */
  function &getData()
  {
      if (isset($this)) {
          if (isset($this->data)) {
              return $this->data;
          } // end if isset this->data
      } // end if isset this

      if (isset($GLOBALS['_validator_data'])) {
          return $GLOBALS['_validator_data'];
      } // end if isset GLOBALS...
  } // end function getData

  /**
   * Checks whether the specified field has errors
   *
   * @author Chris Scharf <scharfie@gmail.com>
   * @version 0.1 5/17/2005
   */
  function hasError($field)
  {
      return isset($this->_validation_errors[$field]);
  } // end function hasError


  /**
   * Returns the error message (if any) for the specified field
   *
   * @author Chris Scharf <scharfie@gmail.com>
   * @version 0.1 5/17/2005
   */
  function getError($field)
  {
      return $this->hasError($field) ? $this->_validation_errors[$field][0] : null;
  } // end function getError

  /**
   * Sets the error message for the specified field
   *
   * * Currently untested *
   * @author Chris Scharf <scharfie@gmail.com>
   * @version 0.1 5/24/2005
   *
   */
  function setError($field, $error)
  {
      $this->_validation_errors[$field] = array($error);
  }
  
  /**
   * Initializes the members
   *
   * @access private
   * @author Chris Scharf <scharfie@gmail.com>
   * @version 0.2 5/16/2005 9:46:59 AM
   */
  function _init(&$obj, &$rules, &$data)
  {
      if ($rules) {
          $this->validate =& $rules;
      } else {
          if (is_object($obj)) {
              if (isset($obj->validate)) {
                  $this->validate =& $obj->validate;
              } // end if isset...
          } // end if obj
      } // end if rules

      if ($data) {
          $this->data =& $data;
      } else {
          if (is_object($obj)) {
              if (isset($obj->params['data'])) {
                  $this->data =& $obj->params['data'];
              } // end if isset...
          } else {
              $method = $_SERVER['REQUEST_METHOD'];
              switch($method) {
              case 'GET':  $this->data =& $_GET;  break;
              case 'POST': $this->data =& $_POST; break;
              } // end
          } // end if obj
      } // end if data
  } // end _init

  /**
   * Filters element values with given filter (such as 'trim')
   * - Currently NOT recursive (5/14/2005 11:40:07 AM)
   *
   * @param element - mixed string element name, array of element names, or ALL_ELEMENTS
   * @param filter  - string string containing function name supporting call_user_func
   * @return filtered elements are updated
   *
   * @author Chris Scharf <scharfie@gmail.com>
   * @version 0.2 5/14/2005 11:40:48 AM
   */
  function applyFilter($element, $filter)
  {
      if (empty($this->data)) {
          return;
      }

      if (!is_array($element)) {
          $element = array($element);
      }

      if ($element[0] == ALL_ELEMENTS) {
          $element = array_keys($this->data);
      }

      foreach($element as $el) {
          $this->data[$el] = call_user_func($filter, $this->data[$el]);
      }
  } // end if

  /**
   * Registers a new validator
   *
   * @param name      - string name for validator (preferably a defined constant)
   * @param type      - string validator type (regex, callback, internal)
                      + regex: regular expression
                      + callback: external callback (separate function)
                      + internal: internal callback (function within $this object)
   * @param validator - string function name or regular expression
   * @param error     - string default error message
   */
  function registerValidator($name, $type, $validator, $error=null)
  {
      $this->validators[$name] = array(
            'type'      => $type,
            'validator' => $validator,
            'error'     => $error
        );
  } // end if

  /**
   * Performs validation rules on specified object
   * ===============
   * Typically, the usage would be as follows:
   *   $this->validate = array(
   *                   'field_name' => array(...),
   *                   'field_name' => array(...)
   *                 )
   *   $this->validator = new Validator(&$this);
   *   if ($this->validator->validates()) { ... }
   * ===============
   * @param obj - mixed object to validate
   */
  function validates($obj=null, $rules=null, $data=null)
  {
      if ($obj != null || $rules != null || $data || null) {
          $this->_init(&$obj, &$rules, &$data);
      } // end if ...
      $data =& $this->data;

      // if data is completely empty, then the form hasn't been submitted
      if (empty($data)) {
          return false;
      }

      $this->_validation_errors = array();
      $this->_getRequiredFields();
      
      // set a global reference to the data for external callbacks
      $GLOBALS['_validator_data'] =& $this->data;
      foreach($this->validate as $field => $validators) {
          // ensure that $validators appears as an array
          // - thanks to darsh for pointing this out
          if (!is_array($validators)) {
              $this->validate[$field] = array($validators);
              $validators =& $this->validate[$field];
          } // end if !is_array...        
          $this->_validateField($data, $field, $validators);
      } // end foreach

      // clear the global reference
      unset($GLOBALS['_validator_data']); // = null;
      return empty($this->_validation_errors);
  } // end if

  /**
   * Creates the _required_fields[] array consisting of all fields
   *   having VALID_NOT_EMPTY rule
   *
   */
  function _getRequiredFields()
  {
      // reset the required fields list
      $this->_required_fields   = array();
      foreach($this->validate as $field => $validators) {
          $required = false;
          foreach($validators as $key => $value) {
              if ($key === VALID_NOT_EMPTY || $value === VALID_NOT_EMPTY) {
                  $required = true;
              } else {
                  if (!empty($value)) {
                      if ($value[0] === VALID_NOT_EMPTY) {
                          $required = true;
                      } // end if value[0]
                  } // end if !empty
              } // end if key === ....
          } // end foreach

          if ($required) {
              $this->_required_fields[$field] = true;
          } // end if required
      } // end foreach
  } // end _getRequiredFields

  /**
   * Performs validation rules on given field
   * - Empty fields are only validated if the VALID_NOT_EMPTY rule is applied
   *   For example, if email_address is an optional field which has the
   *     VALID_EMAIL rule assigned to it, but *not* VALID_NOT_EMPTY, then the
   *     field will not be validated if empty (it will "pass" validation because
   *     the field will not be checked)
   *
   * @param field      - string name of field to validate
   * @param validators - mixed array of validation rules
   *
   * @author Chris Scharf <scharfie@gmail.com>
   * @version 0.1 5/16/2005 9:26:57 AM
   */
  function _validateField(&$data, &$field, &$validators)
  {
      // is the field empty? (no data?)
      if (!isset($data[$field])) {
          // check to see if this field is required
          if (!isset($this->_required_fields[$field])) {
              // field is not required, so field is NOT invalid
              return;
          } else {
              $this->_validation_errors[$field]['ERRORS'] = 1;
              return;
          }// end if
      } // end if !isset

      // process each validator
      foreach($validators as $key=>$value) {
          // check to see if validation rule is "simple"
          //  i.e. ['filename' => array(VALID_NOT_EMPTY);                           ] is simple
          //  and  ['filename' => array(VALID_NOT_EMPTY => 'filename is required'); ] is complex
          //
          //  if rule is simple, then the method is the value, with no arguments
          //    otherwise, the method is the key, and the arguments are in the value
          if (is_int($key)) {
              $method =& $value;
              $args   = null;
          } else {
              $method =& $key;
              $args   =& $value;
          }

          // innocent (valid) until proven guilty (invalid)
          $valid = true;

          // get the validator
          $validator = $this->validators[$method];

          // ensure that the arguments are properly structured
          //   i.e. args must be an array
          if (!is_array($args)) {
              $args = array($args);
          } // end if !is_array...

          // if the first argument (the error message) in args is null,
          //   then use the validators default error message
          if ($args == null || $args[0] == null) {
              $args[0] = $validator['error'];
          } // end if $args == null

          // inpect the validator's type (regex or callback currently)
          switch($validator['type']) {
          case 'regex':
              // regex is easy - validation is simply the return of the preg_match
              $valid = preg_match($validator['validator'], $data[$field]);
              break;

          case 'callback':
          case 'internal':
              // callbacks involve invoking separate functions
              //  the 'validator' key is the function name to call
              $func_name = $validator['validator'];
              $func_args = $args;

              // remove the first argument (the error message)
              array_shift($func_args);

              // shift in the field name
              array_unshift($func_args, $field);

              // for external callbacks, we need to pass the data[] as well
              if ($validator['type'] == 'callback') {
                  array_unshift($func_args, $field);
                  $valid = call_user_func_array($func_name, $func_args);
              } else {
                  $valid = call_user_func_array(array(&$this, $func_name), $func_args);
              }
              break;
          }

          // if validation failed, add the error message to the array
          if (!$valid) {
              $this->_validation_errors[$field][$method] = $args[0];
              $this->_validation_errors[$field][] = $args[0];
              // validation of a field stops once a rule fails
              return;
          }
      } // end foreach
  } // end _validateField

  /**
   * Adds validation rule to specified element(s)
   *
   * @param element - mixed name(s) of elements, or ALL_ELEMENTS
   * @param rule - mixed name of validation rule
   * @param message - string error message to use if validation fails
   * @param ...     - optional arguments specific to validation rule
   *
   * @author Chris Scharf <scharfie@gmail.com>
   * @version 0.2 5/16/2005 9:43:50 AM
   */
  function addRule() // 'email_address', VALID_COMPARE, 'Message', 'email_confirm');
  {
      $args    = func_get_args();
      $argc    = func_num_args();

      $fields  = $args[0];
      $rule    = $args[1];

      if ($argc > 2) {
          $message = $args[2];
          if ($argc > 3) {
              $rule_args = array_slice($args, 2);
          } else {
              $rule_args = $message;
          }
          $rule_arr = array($rule => $rule_args);
      } else {
          $rule_arr = array($rule);
      } // end if argc > 2

      if (!is_array($fields)) {
          $fields = array($fields);
      } // end if !is_array

      foreach($fields as $field) {
          // if the rule is VALID_NOT_EMPTY, update the required fields list
          if ($rule === VALID_NOT_EMPTY) {
              $this->_required_fields[$field] = true;
          } // end if rule === ...

          if (isset($this->validate[$field])) {
              $this->validate[$field] = array_merge($this->validate[$field], $rule_arr); //array_merge($this->validate[$field], $rule);
          } else {
              $this->validate[$field] = $rule_arr;
          } // end if
      } // end foreach
  } // end addRule


  /************ Begin validation callback functions ****************/
  /**
   * Handles VALID_COMPARE validation
   *
   * @param field   - string name of first field
   * @param compare - string name of field to compare with
   * @return true if fields have same value, false otherwise
   *
   * @author Chris Scharf <scharfie@gmail.com>
   * @version 0.1 5/12/2005 12:04:13 AM
   */
  function validatorValidCompare($field, $compare)
  {
      $data =& $this->data;

      // if either field isn't set, exit
      if (!isset($data[$field]) || !isset($data[$compare])) {
          return false;
      } else {
          return ($data[$field] == $data[$compare]);
      } // end if
  } // end if

  /**
   * Handles VALID_RANGE
   * - Validates field against range of min and/or max values
   * - If min is null, the minimum value is unbound
   * - If max is null, the maximum value is unbound
   *
   * @param field - string name of field for validation
   * @param min   - mixed minimum number for range (use null to ignore)
   * @param max   - mixed maximum number for range (use null to ignore)
   * @return true if field is within range, false otherwise
   *
   * @author Chris Scharf <scharfie@gmail.com>
   * @version 0.1 5/13/2005 11:09:47 AM
   */
  function validatorValidRange($field, $min, $max = null)
  {
      $data =& $this->data;

      $val     = $data[$field];
      if ($min != null && $val < $min) {
          return false;
      }

      if ($max != null && $val > $max) {
          return false;
      }

      return true;
  } // end if

  /**
   * Handles VALID_RANGE_LENGTH
   * - Validates field data length against range of min and/or max values
   * - If min is null, the minimum value is unbound
   * - If max is null, the maximum value is unbound
   *
   * @param field - string name of field for validation
   * @param min   - mixed minimum number for range (use null to ignore)
   * @param max   - mixed maximum number for range (use null to ignore)
   * @return true if field is within range, false otherwise
   *
   * @author Chris Scharf <scharfie@gmail.com>
   * @version 0.1 5/13/2005 11:21:40 AM
   */
  function validatorValidRangeLength($field, $min, $max = null)
  {
      $data =& $this->data;
      
      $val     = strlen($data[$field]);
      if ($min != null && $val < $min) {
          return false;
      }

      if ($max != null && $val > $max) {
          return false;
      }

      return true;
  } // end validatorValidRange_length
  /************ End validation callback functions ****************/
  
  function getErrors() {
  	$ary = array();
  	
  	foreach($this->_validation_errors as $k => $v) {
  		$ary[$k] = $v[0];
  	}
  
  	return $ary;
  }

} // end class Validator
?>
