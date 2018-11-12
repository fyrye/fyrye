<?php

namespace FrontEndBundle\Controller;

use Fyrye\Bundle\PhpUnitsOfMeasureBundle\Registry\Manager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * {@inheritDoc}
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", methods={"GET", "POST"})
     */
    public function indexAction()
    {
        return $this->render('FrontEndBundle:Default:index.html.twig');
    }

    /**
     * @Route("/uom/{quantity}/{amount}/{from_unit}/{to_unit}",
     *     name="unit_of_measure",
     *     methods={"GET", "POST"},
     *     requirements={
     *          "quantity": "\w+",
     *          "amount": "\d+",
     *          "from_unit": "\w+",
     *          "to_unit": "\w+"
     *      }
     * )
     * @param \Fyrye\Bundle\PhpUnitsOfMeasureBundle\Registry\Manager $manager
     * @param string $quantity
     * @param int $amount
     * @param string $from_unit
     * @param string $to_unit
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function unitOfMeasureAction(Manager $manager, $quantity, $amount, $from_unit, $to_unit)
    {

        $unit = $manager->getUnit($quantity, $amount, $from_unit);
        $pq = $this->container->get('php_units_of_measure.quantity');
        $mile = $pq->getUnit('length', 1, 'mile');
        $length = $this->container->get('php_units_of_measure.quantity.length');
        $yard = $length->getUnit(1, 'yd');
        $pascal = $pq->getUnit('pressure', 1, 'pascal');
        $viscosity = $this->container->get('php_units_of_measure.quantity.viscosity');
        $stokes = $viscosity->getUnit(1, 'stoke');
        $um = $length->getUnit(1, 'um');

        return $this->render('FrontEndBundle:Default:uom.html.twig', [
                'units' => [
                    ['from' => $from_unit, 'to' => $to_unit, 'amount' => $unit->toUnit($to_unit)],
                    ['from' => 'yard', 'to' => 'feet', 'amount' => $yard->toUnit('ft')],
                    ['from' => 'yard', 'to' => 'inch', 'amount' => $yard->toUnit('in')],
                    ['from' => 'mile', 'to' => 'feet', 'amount' => $mile->toUnit('ft')],
                    ['from' => 'pascal', 'to' => 'psi', 'amount' => $pascal->toUnit('psi')],
                    ['from' => 'stokes', 'to' => 'cst', 'amount' => $stokes->toUnit('cst')],
                    ['from' => 'um', 'to' => 'm', 'amount' => $um->toUnit('m')],
                ],
            ]
        );
    }
}
