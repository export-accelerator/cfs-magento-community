<?php

namespace Overdose\Brands\Api\Data;

/**
 * Interface BrandsInterface
 * @package Overdose\Brands\Api\Data
 */
interface BrandsInterface
{
    const KEY_NAME = 'name';
    const KEY_LOGO = 'logo';
    const KEY_CONTENT = 'content';
    const KEY_IDENTIFIER = 'identifier';
    const KEY_META_TITLE = 'meta_title';
    const KEY_META_DESCRIPTION = 'meta_description';


    /**
     * Get ID
     *
     * @return null|int
     */
    public function getId();

    /**
     * Retrieve Name
     *
     * @return string
     */
    public function getName();

    /**
     * Retrieve Logo
     *
     * @return string
     */
    public function getLogo();

    /**
     * Retrieve Identifier
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Retrieve Brand Content
     *
     * @return string
     */
    public function getContent();

    /**
     * @return string
     */
    public function getMetaTitle();

    /**
     * @return string
     */
    public function getMetaDescription();

    /**
     * Set ID
     *
     * @param int $value
     * @return \Overdose\Brands\Api\Data\BrandsInterface
     */
    public function setId($value);

    /**
     * Set Name
     *
     * @param string $value
     * @return \Overdose\Brands\Api\Data\BrandsInterface
     */
    public function setName($value);

    /**
     * Set Logo
     *
     * @param string $value
     * @return \Overdose\Brands\Api\Data\BrandsInterface
     */
    public function setLogo($value);

    /**
     * Set Identifier
     *
     * @param string $value
     * @return \Overdose\Brands\Api\Data\BrandsInterface
     */
    public function setIdentifier($value);

    /**
     * Set Content
     *
     * @param string $value
     * @return \Overdose\Brands\Api\Data\BrandsInterface
     */
    public function setContent($value);

    /**
     * @param string $value
     * @return \Overdose\Brands\Api\Data\BrandsInterface
     */
    public function setMetaTitle($value);

    /**
     * @param string $value
     * @return \Overdose\Brands\Api\Data\BrandsInterface
     */
    public function setMetaDescription($value);
}
