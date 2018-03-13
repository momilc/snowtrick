<?php
/**
 * Created by IntelliJ IDEA.
 * User: lsm
 * Date: 06/03/18
 * Time: 17:57
 */

namespace App\Entity;


abstract class FigureAddEvent extends Figure
{
    private $figure;

    public function __construct(Figure $figure)
    {
        parent::__construct();
        $this->figure = $figure;
    }

    /**
     * @return mixed
     */
    public function getFigure(): ?Figure
    {
        return $this->figure;
    }


}