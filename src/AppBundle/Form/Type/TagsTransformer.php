<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Tag;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TagsTransformer implements DataTransformerInterface {


    public function transform($value)
    {
        return implode(', ', $value);
    }


    public function reverseTransform($value)
    {
        $tagname = explode(',', $value);
        $tags = [];
        foreach ($tagname as $name) {
            $tag = new Tag();
            $tag->setName($name);
            $tags[] = $tag;
        }
        return $tags;
    }
}