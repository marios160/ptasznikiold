<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Magazyn
 *
 * @ORM\Table(name="magazyny")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MagazynRepository")
 */
class Magazyn
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nazwa", type="string", length=30)
     */
    private $nazwa;

    /**
     * @var string
     *
     * @ORM\Column(name="nazwa_skrocona", type="string", length=10)
     */
    private $nazwaSkrocona;
    
    /**
     * * @ORM\OneToMany(targetEntity="Ptasznik", mappedBy="magazyn")
     */
    protected $ptaszniki;
    
     public function __construct()
     {
         $this->ptaszniki = new ArrayCollection();
     }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nazwa
     *
     * @param string $nazwa
     *
     * @return Magazyn
     */
    public function setNazwa($nazwa)
    {
        $this->nazwa = $nazwa;

        return $this;
    }

    /**
     * Get nazwa
     *
     * @return string
     */
    public function getNazwa()
    {
        return $this->nazwa;
    }

    /**
     * Set nazwaSkrocona
     *
     * @param string $nazwaSkrocona
     *
     * @return Magazyn
     */
    public function setNazwaSkrocona($nazwaSkrocona)
    {
        $this->nazwaSkrocona = $nazwaSkrocona;

        return $this;
    }

    /**
     * Get nazwaSkrocona
     *
     * @return string
     */
    public function getNazwaSkrocona()
    {
        return $this->nazwaSkrocona;
    }

    /**
     * Add ptaszniki
     *
     * @param \AppBundle\Entity\Ptasznik $ptaszniki
     *
     * @return Magazyn
     */
    public function addPtaszniki(\AppBundle\Entity\Ptasznik $ptaszniki)
    {
        $this->ptaszniki[] = $ptaszniki;

        return $this;
    }

    /**
     * Remove ptaszniki
     *
     * @param \AppBundle\Entity\Ptasznik $ptaszniki
     */
    public function removePtaszniki(\AppBundle\Entity\Ptasznik $ptaszniki)
    {
        $this->ptaszniki->removeElement($ptaszniki);
    }

    /**
     * Get ptaszniki
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPtaszniki()
    {
        return $this->ptaszniki;
    }
}
