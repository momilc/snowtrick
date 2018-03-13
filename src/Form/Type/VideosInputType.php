<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Type;

use App\Form\DataTransformer\StyleArrayToStringTransformer;
use App\Form\DataTransformer\TagArrayToStringTransformer;
use App\Form\DataTransformer\VideoArrayToStringTransformer;
use App\Repository\StyleRepository;
use App\Repository\TagRepository;
use App\Repository\VideoRepository;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Defines the custom form field type used to manipulate tags values across
 * Bootstrap-tagsinput javascript plugin.
 *
 * See https://symfony.com/doc/current/cookbook/form/create_custom_field_type.html
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class VideosInputType extends AbstractType
{
    private $videos;

    public function __construct(VideoRepository $videos)
    {
        $this->videos = $videos;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addModelTransformer(new CollectionToArrayTransformer(), true)
            //Collection <-> array <-> string
            ->addModelTransformer(new VideoArrayToStringTransformer($this->videos), true);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['videos'] = $this->videos->findAll();
    }

    public function getParent()
    {
        return TextType::class;
    }

}
