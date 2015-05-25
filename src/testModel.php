<?php


/**
 * @method setCustomers_id()
 * @method getCustomers_id()
 * @method setHb_rabatt()
 * @method getHb_rabatt()
 * @method setNon_hb_rabatt()
 * @method getNon_hb_rabatt()
 * @method setRabatt_1()
 * @method getRabatt_1()
 * @method setRabatt_2()
 * @method getRabatt_2()
 * @method setRabatt_3()
 * @method getRabatt_3()
 * @method setRabatt_4()
 * @method getRabatt_4()
 * @method setCustomers_cid()
 * @method getCustomers_cid()
 * @method setCustomers_vat_id()
 * @method getCustomers_vat_id()
 * @method setCustomers_vat_id_status()
 * @method getCustomers_vat_id_status()
 * @method setCustomers_warning()
 * @method getCustomers_warning()
 * @method setCustomers_status()
 * @method getCustomers_status()
 * @method setCustomers_gender()
 * @method getCustomers_gender()
 * @method setCustomers_firstname()
 * @method getCustomers_firstname()
 * @method setCustomers_lastname()
 * @method getCustomers_lastname()
 * @method setCustomers_dob()
 * @method getCustomers_dob()
 * @method setCustomers_email_address()
 * @method getCustomers_email_address()
 * @method setCustomers_default_address_id()
 * @method getCustomers_default_address_id()
 * @method setCustomers_telephone()
 * @method getCustomers_telephone()
 * @method setCustomers_fax()
 * @method getCustomers_fax()
 * @method setCustomers_password()
 * @method getCustomers_password()
 * @method setCustomers_newsletter()
 * @method getCustomers_newsletter()
 * @method setCustomers_newsletter_mode()
 * @method getCustomers_newsletter_mode()
 * @method setMember_flag()
 * @method getMember_flag()
 * @method setDelete_user()
 * @method getDelete_user()
 * @method setAccount_type()
 * @method getAccount_type()
 * @method setPassword_request_key()
 * @method getPassword_request_key()
 * @method setPayment_unallowed()
 * @method getPayment_unallowed()
 * @method setShipping_unallowed()
 * @method getShipping_unallowed()
 * @method setRefferers_id()
 * @method getRefferers_id()
 * @method setCustomers_date_added()
 * @method getCustomers_date_added()
 * @method setCustomers_last_modified()
 * @method getCustomers_last_modified()
 * @method setGrosshandel()
 * @method getGrosshandel()
 * @method setOtsr()
 * @method getOtsr()
 * @method setCustomer_bank_name()
 * @method getCustomer_bank_name()
 * @method setCustomer_kto()
 * @method getCustomer_kto()
 * @method setCustomer_blz()
 * @method getCustomer_blz()
 * @method setCustomers_manufacturers_export()
 * @method getCustomers_manufacturers_export()
 * @method setCustomers_languages_export()
 * @method getCustomers_languages_export()
 * @method setWholesale_min_stock()
 * @method getWholesale_min_stock()
 * @method setBonus_points()
 * @method getBonus_points()
 * @method setInfomail()
 * @method getInfomail()
 * @method setInfomail_subscr()
 * @method getInfomail_subscr()
 *
 * @package MeinPaket\xtModels
 */
class testModel extends \SimpleModel\SimpleModel
{
    protected $_tableName = "customers";
    protected $_primaryKey = "customers_id";
    protected $_metaData
        = array(
            'customers_id'                   => array('type' => 'integer'),
            // int(11) NOT NULL AUTO_INCREMENT,
            'hb_rabatt'                      => array('type' => 'int'),
            // int(11) NOT NULL,
            'non_hb_rabatt'                  => array('int' => 'int'),
            // int(11) NOT NULL,
            'rabatt_1'                       => array('type' => 'int',),
            // int(11) NOT NULL,
            'rabatt_2'                       => array('type' => 'int',),
            // int(11) NOT NULL COMMENT 'ex non_hb_rabatt',
            'rabatt_3'                       => array('type' => 'int',),
            // int(11) NOT NULL COMMENT 'ex hb_rabatt',
            'rabatt_4'                       => array('type' => 'int',),
            // int(11) NOT NULL COMMENT 'Sonderaktionen',
            'customers_cid'                  => array('type' => 'int'),
            // int(11) NOT NULL DEFAULT '0',
            'customers_vat_id'               => array('type' => 'string', 'maxLength' => 20),
            // varchar(20) DEFAULT NULL,
            'customers_vat_id_status'        => array('type' => 'int'),
            // int(2) NOT NULL DEFAULT '0',
            'customers_warning'              => array('type' => 'string', 'maxLength' => 32),
            // varchar(32) DEFAULT NULL,
            'customers_status'               => array('type' => 'int',),
            // int(5) NOT NULL DEFAULT '1',
            'customers_gender'               => array('type' => 'string', 'maxLength' => 1),
            // char(1) NOT NULL DEFAULT '',
            'customers_firstname'            => array('type' => 'string', 'maxLength' => 32),
            // varchar(32) NOT NULL DEFAULT '',
            'customers_lastname'             => array('type' => 'string', 'maxLength' => 32),
            // varchar(32) NOT NULL DEFAULT '',
            'customers_dob'                  => array('type' => 'string'),
            // datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            'customers_email_address'        => array('type' => 'string', 'maxLength' => 96),
            // varchar(96) NOT NULL DEFAULT '',
            'customers_default_address_id'   => array('type' => 'int', 'default' => 0),
            // int(11) NOT NULL DEFAULT '0',
            'customers_telephone'            => array('type' => 'string', 'maxLength' => 32),
            // varchar(32) NOT NULL DEFAULT '',
            'customers_fax'                  => array('type' => 'string', 'maxLength' => 32),
            // varchar(32) DEFAULT NULL,
            'customers_password'             => array('type' => 'string', 'maxLength' => 40),
            // varchar(40) NOT NULL DEFAULT '',
            'customers_newsletter'           => array('type' => 'string', 'maxLength' => 1),
            // char(1) DEFAULT NULL,
            'customers_newsletter_mode'      => array('type' => 'string', 'maxLength' => 1),
            // char(1) NOT NULL DEFAULT '0',
            'member_flag'                    => array('type' => 'string', 'maxLength' => 1),
            // char(1) NOT NULL DEFAULT '0',
            'delete_user'                    => array('type' => 'string', 'maxLength' => 1),
            // char(1) NOT NULL DEFAULT '1',
            'account_type'                   => array('type' => 'string', 'maxLength' => 1),
            // int(1) NOT NULL DEFAULT '0',
            'password_request_key'           => array('type' => 'string', 'maxLength' => 32),
            // varchar(32) NOT NULL DEFAULT '',
            'payment_unallowed'              => array('type' => 'string', 'maxLength' => 255),
            // varchar(255) NOT NULL DEFAULT '',
            'shipping_unallowed'             => array('type' => 'string', 'maxLength' => 255),
            // varchar(255) NOT NULL DEFAULT '',
            'refferers_id'                   => array('type' => 'int',),
            // int(5) NOT NULL DEFAULT '0',
            'customers_date_added'           => array('type' => 'dateTime'),
            // datetime DEFAULT '0000-00-00 00:00:00',
            'customers_last_modified'        => array('type' => 'dateTime'),
            // datetime DEFAULT '0000-00-00 00:00:00',
            'grosshandel'                    => array('type' => 'int',),
            // int(1) NOT NULL DEFAULT '0',
            'otsr'                           => array('type' => 'int'),
            // tinyint(1) NOT NULL,
            'customer_bank_name'             => array('type' => 'string', 'maxLength' => 256),
            // varchar(256) NOT NULL,
            'customer_kto'                   => array('type' => 'string', 'maxLength' => 64),
            // varchar(64) NOT NULL,
            'customer_blz'                   => array('type' => 'string', 'maxLength' => 64),
            // varchar(20) NOT NULL,
            'customers_manufacturers_export' => array('type' => 'string', 'maxLength' => 16777215),
            // mediumtext,
            'customers_languages_export'     => array('type' => 'string', 'maxLength' => 16777215),
            // mediumtext,
            'wholesale_min_stock'            => array('type' => 'int'),
            // int(3) NOT NULL DEFAULT '10',
            'bonus_points'                   => array('type' => 'int',),
            // int(11) NOT NULL DEFAULT '0',
            'infomail'                       => array('type' => 'int',),
            // tinyint(1) NOT NULL DEFAULT '1',
            'infomail_subscr'                => array('type' => 'int'),
            // tinyint(1) NOT NULL DEFAULT '0',
        );

    /**
     * To address an array element use $key.$key2 notation
     * To address an object element use $key->$key2 notation @todo
     * @var array
     */
    protected $_mappings
        = array(
            'customers_id'            => array('default' => null),
            'customers_gender'        => array('target' => 'billingaddress.salutation',
                'converter'=>'mapGender'),
            'customers_status'        => array('default' => '2'),
            'customers_firstname'     => array('target' => 'billingaddress.firstname'),
            'customers_lastname'      => array('target' => 'billingaddress.lastname'),
            'customers_email_address' => array('target' => 'additional.user.email'),
            'customers_telephone'     => array('target' => 'billingaddress.phone'),
            'customers_newsletter'    => array('target' => 'additional.user.newsletter'),
            'customers_password'      => array('parameters' => 'password'),
            'customers_date_added'    => array('default' => 'now()'),
            'customers_last_modified' => array('default' => ''),
            'delete_user'             => array('default' => '0'),
            'account_type'            => array('default' => '1'),
            'customers_dob'           => array('target' => 'billingaddress.birthday')
        );

    public function mapGender($swGender)
    {
        switch ($swGender) {
            case 'mr':
                return "m";
            default  :
                return 'f';
        }
    }
    public function __construct($configs = array())
    {
        parent::__construct();

        if (is_array($configs)) {
            foreach ($configs as $key => $configValue) {
                $method = 'set' . ucfirst($key);
                $this->$method($configValue);
            }
        }
        $this->mandatoryCheck();
    }

    public function isEmpty()
    {
        return is_null($this->getCustomers_id());
    }

    /**
     * Loads a XTC Customer from the DB using the email address as primary key
     *
     * @param $email
     * @return bool
     */
    public function getXtcUserByEMail($email)
    {
        if (empty($email)) {
            return false;
        }
        $sql = "SELECT " . implode(
                ',',
                $this->makeDbFields('c')
            ) . " FROM " . $this->escapeIdentifier(
                $this->_tableName
            ) . ' c WHERE c.`customers_email_address` = ? ORDER BY `customers_last_modified` LIMIT 1';
        $sth = $this->Db()->prepare($sql);
        $sth->execute(array($email));
        $data = $sth->fetchAll(\PDO::FETCH_ASSOC);
        if (!empty($data)) {

            $this->init($data[0]);
            return true;
        } else {
            return false;
        }
    }


}
