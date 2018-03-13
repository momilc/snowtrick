<?php

namespace App\Form\DataTransformer;

use App\Entity\Style;
use App\Entity\Tag;
use App\Entity\Video;
use App\Repository\StyleRepository;
use App\Repository\TagRepository;
use App\Repository\VideoRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoArrayToStringTransformer implements DataTransformerInterface
{
    private $videos;

    public function __construct(VideoRepository $videos)
    {
        $this->videos = $videos;
    }

    /**
     * Transforms a value from the original representation to a transformed representation.
     *
     * This method is called on two occasions inside a form field:
     *
     * 1. When the form field is initialized with the data attached from the datasource (object or array).
     * 2. When data from a request is submitted using {@link Form::submit()} to transform the new input data
     *    back into the renderable format. For example if you have a date field and submit '2009-10-10'
     *    you might accept this value because its easily parsed, but the transformer still writes back
     *    "2009/10/10" onto the form field (for further displaying or other purposes).
     *
     * This method must be able to deal with empty values. Usually this will
     * be NULL, but depending on your implementation other empty values are
     * possible as well (such as empty strings). The reasoning behind this is
     * that value transformers must be chainable. If the transform() method
     * of the first value transformer outputs NULL, the second value transformer
     * must be able to process that value.
     *
     * By convention, transform() should return an empty string if NULL is
     * passed.
     *
     * @param $videos
     * @return mixed The value in the transformed representation
     *
     */
    public function transform($videos): string
    {
        return implode(',', $videos);
    }

    /**
     * Transforms a value from the transformed representation to its original
     * representation.
     *
     * This method is called when {@link Form::submit()} is called to transform the requests tainted data
     * into an acceptable format for your data processing/model layer.
     *
     * This method must be able to deal with empty values. Usually this will
     * be an empty string, but depending on your implementation other empty
     * values are possible as well (such as NULL). The reasoning behind
     * this is that value transformers must be chainable. If the
     * reverseTransform() method of the first value transformer outputs an
     * empty string, the second value transformer must be able to process that
     * value.
     *
     * By convention, reverseTransform() should return NULL if an empty string
     * is passed.
     *
     * @param $string
     * @return mixed The value in the original representation
     *
     */
    public function reverseTransform($string): array
    {
        if('' === $string || null === $string){
            return [];
        }

        $urls = array_filter(array_unique(array_map('trim', explode(',', $string))));
        // Recherche des styles actuels et recherches des nouveaux styles à creer
        $videos = $this->videos->findBy([
            'url' => $urls
        ]);

        $newUrls = array_diff($urls, $videos);
        foreach ($newUrls as $url){
            $video = new Video();
            $video->setUrl($url);
            $videos[] = $video;
            // Pas de Persist ici car dans l'entité Figure les propriété de $tags inclue une cascade={"persist"}
        }
        // On retourne un tableau de $tags qui sera transformée en une ArrayCollection par Doctrine via l'entité Figure
        return $videos;
    }
}
