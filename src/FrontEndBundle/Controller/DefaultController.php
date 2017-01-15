<?php
namespace FrontEndBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('FrontEndBundle:Default:index.html.twig');
    }

    /**
     * @Route("/uom", name="unit_of_measure")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function unitOfMeasureAction()
    {
        $quantity = $this->container->get('php_units_of_measure.quantity');
        $mile = $quantity->getUnit('length', 1, 'mile');
        $length = $this->container->get('php_units_of_measure.quantity.length');
        $yard = $length->getUnit(1, 'yd');
        $another = $length->getUnit(2, 'yd');
        $pascal = $quantity->getUnit('pressure', '1', 'pascal');

        return $this->render(
            'FrontEndBundle:Default:uom.html.twig',
            [
                'feet' => $yard->toUnit('ft'),
                'inches' => $yard->toUnit('in'),
                'feet_2' => $another->toUnit('ft'),
                'inches_2' => $another->toUnit('in'),
                'mile_feet' => $mile->toUnit('ft'),
                'psi' => $pascal->toUnit('psi'),
            ]
        );
    }
}
