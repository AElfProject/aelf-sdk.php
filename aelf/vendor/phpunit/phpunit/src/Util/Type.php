<?php

declare(strict_types=1);

namespace BitWasp\Buffertools;

class TemplateFactory
{
    /**
     * @var \BitWasp\Buffertools\Template
     */
    private $template;

    /**
     * @var TypeFactoryInterface
     */
    private $types;

    /**
     * TemplateFactory constructor.
     * @param Template|null $template
     * @param TypeFactoryInterface|null $typeFactory
     */
    public function __construct(Template $template = null, TypeFactoryInterface $typeFactory = null)
    {
        $this->template = $template ?: new Template();
        $this->types = $typeFactory ?: new CachingTypeFactory();
    }

    /**
     * Return the Template as it stands.
     *
     * @return Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Add a Uint8 serializer to the template
     *
     * 