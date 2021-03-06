<?php

namespace Dywee\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dywee\CoreBundle\Model\UserInterface;
use Dywee\CoreBundle\Traits\Seo;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * Page
 *
 * @ORM\Table(name="pages")
 * @ORM\Entity(repositoryClass="Dywee\CMSBundle\Repository\PageRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\Tree(type="nested")
 */
class Page implements Translatable
{
    use Seo;

    const TYPE_HOMEPAGE = 'cms.type.homepage';
    const TYPE_NORMALPAGE = 'cms.type.normal';

    const STATE_PUBLISHED = 'cms.state.published';
    const STATE_DRAFT = 'cms.state.draft';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(type="string", length=50)
     */
    private $type = self::TYPE_NORMALPAGE;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $inMenu;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $menuOrder;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $active = true;

    /**
     * @var integer
     *
     * @ORM\Column(type="string", length=50)
     */
    private $state = self::STATE_DRAFT;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=255)
     *
     */
    private $name;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    private $menuName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updateDate", type="datetime")
     */
    private $updateDate;

    /**
     * @ORM\Column(name="childArguments", type="string", length=255, nullable=true)
     */
    private $childArguments;

    /**
     * @ORM\Column(name="template", type="string", length=255)
     */
    private $template = 'default';

    /**
     * @ORM\ManyToOne(targetEntity="Dywee\CoreBundle\Model\UserInterface")
     */
    private $updatedBy;

    /**
     * @ORM\OneToMany(targetEntity="Dywee\CMSBundle\Entity\PageStat", mappedBy="page", cascade={"persist", "remove"})
     */
    private $pageStat;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Dywee\CMSBundle\Entity\Page", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Dywee\CMSBundle\Entity\Page", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * @ORM\OneToMany(targetEntity="PageElement", mappedBy="page", cascade={"persist", "remove"})
     */
    private $pageElements;


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
     * Set type
     *
     * @param integer $type
     * @return Page
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set inMenu
     *
     * @param boolean $inMenu
     * @return Page
     */
    public function setInMenu($inMenu)
    {
        $this->inMenu = $inMenu;

        return $this;
    }

    /**
     * Get inMenu
     *
     * @return boolean
     */
    public function getInMenu()
    {
        return $this->inMenu;
    }

    /**
     * Set menuOrder
     *
     * @param integer $menuOrder
     * @return Page
     */
    public function setMenuOrder($menuOrder)
    {
        $this->menuOrder = $menuOrder;

        return $this;
    }

    /**
     * Get menuOrder
     *
     * @return integer
     */
    public function getMenuOrder()
    {
        return $this->menuOrder;
    }


    /**
     * Set active
     *
     * @param boolean $active
     * @return Page
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return Page
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->creationDate = new \DateTime();
        $this->updateDate = new \DateTime();
        $this->createdAt = new \DateTime();
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Page
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Page
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        if ($this->content != null) {
            return $this->content;
        }

        $content = '';

        foreach ($this->getPageElements() as $pageElement) {
            $content .= $pageElement->getHtml();
        }

        return $content;
    }

    /**
     * Set menuName
     *
     * @param string $menuName
     * @return Page
     */
    public function setMenuName($menuName)
    {
        $this->menuName = $menuName;

        return $this;
    }

    /**
     * Get menuName
     *
     * @return string
     */
    public function getMenuName()
    {
        return $this->menuName;
    }

    /**
     * Set updateDate
     *
     * @param \DateTime $updateDate
     * @return Page
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    //Nouvelle norme des dates
    public function setUpdatedAt($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    //Nouvelle norme des dates
    public function getUpdatedAt()
    {
        return $this->updateDate;
    }

    /**
     * @ORM\PreUpdate
     */
    public function updateUpdateDate()
    {
        $this->setUpdateDate(new \Datetime());
    }

    public function getUrl()
    {
        return $this->getSeoUrl() != '' ? $this->getSeoUrl() : $this->getId();
    }

    /**
     * Set parent
     *
     * @param Page $parent
     * @return Page
     */
    public function setParent(Page $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Page
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add childs
     *
     * @param Page $child
     * @return Page
     */
    public function addChild(Page $child)
    {
        $this->children[] = $child;
        $child->setParent($this);

        return $this;
    }

    /**
     * Remove child
     *
     * @param Page $child
     */
    public function removeChild(Page $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get childs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set childArguments
     *
     * @param string $childArguments
     * @return Page
     */
    public function setChildArguments($childArguments)
    {
        $this->childArguments = $childArguments;

        return $this;
    }

    /**
     * Get childArguments
     *
     * @return string
     */
    public function getChildArguments()
    {
        return $this->childArguments;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Page
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set template
     *
     * @param string $template
     *
     * @return Page
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set updatedBy
     *
     * @param UserInterface $updatedBy
     * @return Page
     */
    public function setUpdatedBy(UserInterface $updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return UserInterface
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Add pageStat
     *
     * @param PageStat $pageStat
     * @return Page
     */
    public function addPageStat(PageStat $pageStat)
    {
        $this->pageStat[] = $pageStat;

        return $this;
    }

    /**
     * Remove pageStat
     *
     * @param PageStat $pageStat
     */
    public function removePageStat(PageStat $pageStat)
    {
        $this->pageStat->removeElement($pageStat);
    }

    /**
     * Get pageStat
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPageStat()
    {
        return $this->pageStat;
    }

    /**
     * Set lft
     *
     * @param integer $lft
     * @return Page
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Get lft
     *
     * @return integer
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set lvl
     *
     * @param integer $lvl
     * @return Page
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * Get lvl
     *
     * @return integer
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Set rgt
     *
     * @param integer $rgt
     * @return Page
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Get rgt
     *
     * @return integer
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set root
     *
     * @param integer $root
     * @return Page
     */
    public function setRoot($root)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * Get root
     *
     * @return integer
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Add pageElement
     *
     * @param PageElement $pageElement
     * @return Page
     */
    public function addPageElement(PageElement $pageElement)
    {
        $this->pageElements[] = $pageElement;
        $pageElement->setPage($this);

        return $this;
    }

    /**
     * Remove pageElement
     *
     * @param PageElement $pageElement
     */
    public function removePageElement(PageElement $pageElement)
    {
        $this->pageElements->removeElement($pageElement);
        $pageElement->setPage(null);
    }

    /**
     * Get pageElements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPageElements()
    {
        return $this->pageElements;
    }

    public function cheatingTrick()
    {
        return null;
    }

    public function hasForm()
    {
        foreach ($this->getPageElements() as $pageElement) {
            if ($pageElement->getType() == 'form') {
                return true;
            }
        }

        return false;
    }

    public function getForms()
    {
        $return = array();
        foreach ($this->getPageElements() as $pageElement) {
            if ($pageElement->getType() == 'form') {
                $return[] = $pageElement->getContent();
            }
        }
        return $return;
    }

    static function getConstantList()
    {
        $oClass = new \ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }
}
