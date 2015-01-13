<?php
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * address_book.
 * Date: 13/01/15
 */
class TwigEngine implements EngineInterface
{

    /** @var Twig_Environment */
    protected $environment;
    /** @var TemplateNameParserInterface */
    protected $parser;

    /**
     * Constructor.
     *
     * @param \Twig_Environment           $environment A \Twig_Environment instance
     * @param TemplateNameParserInterface $parser      A TemplateNameParserInterface instance
     * @param string                      $dir         Templates directory
     */
    public function __construct(\Twig_Environment $environment, TemplateNameParserInterface $parser, $dir)
    {
        $loader = new Twig_Loader_Filesystem($dir);

        $this->environment = $environment;
        $this->environment->setLoader($loader);

        $this->parser = $parser;
    }

    /**
     * Renders a template.
     *
     * @param string|TemplateReferenceInterface $name       A template name or a TemplateReferenceInterface instance
     * @param array                             $parameters An array of parameters to pass to the template
     *
     * @return string The evaluated template as a string
     *
     * @throws \RuntimeException if the template cannot be rendered
     *
     * @api
     */
    public function render($name, array $parameters = array())
    {
        return $this->environment->loadTemplate((string)$name)->render($parameters);
    }

    /**
     * Returns true if the template exists.
     *
     * @param string|TemplateReferenceInterface $name A template name or a TemplateReferenceInterface instance
     *
     * @return Boolean true if the template exists, false otherwise
     *
     * @throws \RuntimeException if the engine cannot handle the template name
     *
     * @api
     */
    public function exists($name)
    {
        $loader = $this->environment->getLoader();

        return $loader->exists($name);
    }

    /**
     * Returns true if this class is able to render the given template.
     *
     * @param string|TemplateReferenceInterface $name A template name or a TemplateReferenceInterface instance
     *
     * @return Boolean true if this class supports the given template, false otherwise
     *
     * @api
     */
    public function supports($name)
    {
        if ($name instanceof \Twig_Template) {
            return true;
        }

        $template = $this->parser->parse($name);

        return 'twig' === $template->get('engine');
    }
}