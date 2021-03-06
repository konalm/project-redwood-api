<?php

  namespace AppBundle\Entity;

  use Doctrine\ORM\Mapping as ORM;

  /**
   * @ORM\Entity
   * @ORM\Table(name="client")
   */
  class client
  {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=100)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=300)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $postcode;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
      return $this->id;
    }


    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return client
     */
    public function setFirstName($firstName)
    {
      $this->first_name = $firstName;

      return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
      return $this->first_name;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return client
     */
    public function setLastName($lastName)
    {
      $this->last_name = $lastName;

      return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
      return $this->last_name;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return client
     */
    public function setEmail($email)
    {
      $this->email = $email;

      return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
      return $this->email;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return client
     */
    public function setAddress($address)
    {
      $this->address = $address;

      return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set postcode
     *
     * @param string $postcode
     *
     * @return client
     */
    public function setPostcode($postcode)
    {
      $this->postcode = $postcode;

      return $this;
    }

    /**
     * Get postcode
     *
     * @return string
     */
    public function getPostcode()
    {
      return $this->postcode;
    }


    public function setAll($clientData)
    {
      $this->setFirstName($clientData->firstName);
      $this->setLastName($clientData->lastName);
      $this->setEmail($clientData->email);
      $this->setAddress($clientData->address);
      $this->setCity($clientData->city);
      $this->setPostcode($clientData->postcode);
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return client
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }
}
