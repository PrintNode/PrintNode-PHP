<?php

namespace PrintNode;

/**
 * Credentials
 *
 * @desc Used Request when communicating with API server.
 */
abstract class Credentials
{
    
    /**
     * API Key to be used in the authentication header
     * @var string 
     */
    public $apiKey;
    
    /**
     * Username to be used in the authentication header
     * @var string 
     */
    public $username;
    
    /**
     * Password to be used in the authentication header
     * @var string 
     */
    public $password;
    
    /**
     * Child Account Email Address
     * @var string 
     */
    public $childAccountEmail;
    
    /**
     * Child Account Creator Reference
     * @var string 
     */
    public $childAccountCreatorRef;
    
    /**
     * Child Account Id
     * @var string 
     */
    public $childAccountId;
    
    /**
     * Sets the authentication context to a child account by the email address
     * assigned to the child acount.
     * 
     * @param string $email Email address 
     * @return boolean
     */
    public function setChildAccountByEmail($email)
    {
        
        $this->clearChildCredentials();
        $this->childAccountEmail = $email;
        
        return true;
        
    }
    
    /**
     * Sets the authentication context to a child account by the creator
     * reference assigned to the child acount.
     * 
     * @param string $creatorRef Creator reference id
     * @return boolean
     */
    public function setChildAccountByCreatorRef($creatorRef)
    {
        
        $this->clearChildCredentials();
        $this->childAccountCreatorRef = $creatorRef;
        
        return true;
        
    }
    
    /**
     * Sets the authentication context to a child account by the account id
     * assigned to the child acount.
     * 
     * @param string $id Account Id
     * @return boolean
     */
    public function setChildAccountById($id)
    {
        
        $this->clearChildCredentials();
        $this->childAccountId = $id;
        
        return true;
        
    }
    
    /**
     * Clears all existing credential properties
     * @return boolean
     */
    public function clearChildCredentials()
    {
        
        $this->childAccountEmail = null;    
        $this->childAccountCreatorRef = null;
        $this->childAccountId = null;
        
        return true;
        
    }
    
}
