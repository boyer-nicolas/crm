<?php

namespace Niwee\Trident\Core;

use Konekt\PdfInvoice\InvoicePrinter;

final class PDFGenerator
{
    private $items = [];
    private $invoice;
    /**
     * @var string
     */
    private $type;

    /**
     * Generate an Invoice
     *
     * @param string $type The type of pdf to generate (1 = Facture, 2 = Devis, 3 = Accompte)
     */
    public function generate(int $type = 1, string $accent_color, array $emitter, array $receiver, array $items, bool $paid, string $note_title, string $note_paragraph)
    {
        $this->db = new Database();
        $this->invoice = new InvoicePrinter('A4', '€', 'fr');

        // Set invoice type
        switch ($type)
        {
            case 2:
                $this->type = "Devis";
                break;
            case 3:
                $this->type = "Accompte";
                break;
            default:
                $this->type = "Facture";
                break;
        }

        $this->invoice->setType($this->type);

        // Init timezone and date
        $date = new \DateTime('now');
        $tz = new \DateTimeZone('Europe/Paris');
        $date->setTimeZone($tz);

        // Set current date
        $current_date = $date->format('d/m/Y');

        // Set current time
        $current_time = $date->format('H:i:s');

        // Create due time in two weeks
        $due = $date->modify('+2 weeks');
        $due = $due->format('d/m/Y');

        // Set timezone
        $this->invoice->setTimezone('Europe/Paris');

        // Set format
        $this->invoice->setNumberFormat(',', ' ', 'right', false, false);

        // Set logo
        $this->invoice->setLogo($_SERVER['DOCUMENT_ROOT'] . '/public/images/logo/logo.png');

        // Set color accent
        if ($accent_color)
        {
            $this->invoice->setColor($accent_color);
        }
        else
        {
            $this->invoice->setColor("#f28526");
        }

        // Set reference
        $this->invoice->setReference("NIW-1");

        // Invert the positions of the company information and the client information
        $this->invoice->flipflop();

        // Setinvoice date, time and due from earlier calculations
        $this->invoice->setDate('         ' . $current_date);   //Billing Date
        $this->invoice->setTime('         ' . $current_time);   //Billing Time
        $this->invoice->setDue('         ' . $due);    // Due Date

        // Set our information
        $this->invoice->setFrom(
            [
                "NiWee Productions",
                "NiWee Productions",
                "8-10 Allée Evariste Galois",
                "63000 Clermont-Ferrand"
            ]
        );

        // Set client information
        $this->invoice->setTo(
            [
                "Nom du client",
                "Société du client",
                "Adresse de facturation",
                "00000 Ville"
            ]
        );

        // Add items
        $this->addItem(
            "Création de Logo",
            "Logo format PNG, 
            SVG et JPG",
            1,
            0,
            350,
            0,
            350
        );

        $this->addItem(
            "Site internet Vitrine",
            "Site contenant 5 pages avec contenu rédigé.",
            1,
            0,
            2460,
            0,
            2460
        );

        // Set total without VAT
        $total = $this->getTotal();

        // Set total with VAt
        $totalWithVAT = $this->getTotalWithVAT($total);

        // Get VAT Amount
        $vat = $this->getVAT($totalWithVAT, $total);

        // Add totals
        $this->invoice->addTotal("Total HT", $total);
        $this->invoice->addTotal("TVA 20%", $vat);
        $this->invoice->addTotal("Total TTC", $totalWithVAT, true);

        // Add badge

        if ($paid)
        {
            $this->invoice->addBadge("PAYÉ");
        }
        else
        {
            $this->invoice->addBadge("A PAYER");
        }

        // Add note
        if ($note_title)
        {
            $this->invoice->addTitle("Message Important");
        }
        if ($note_paragraph)
        {
            $this->invoice->addParagraph("Ce facture constitue un accompte de la précédente facture que vous avez reçu.");
        }

        $this->invoice->setFooternote("NiWee Productions");

        // Render
        $this->invoice->render($_SERVER['DOCUMENT_ROOT'] . '/static/pdf/invoice.pdf', 'F');
    }

    public function getVAT(float $totalWithVat, float $total)
    {
        return $totalWithVat - $total;
    }

    /**
     * Add an item to the invoice
     *
     * @param  string $name        The name of the item
     * @param  string $description The description of the item
     * @param  int    $quantity    The quantity of the item
     * @param  float  $price       The price of the item
     * @param  float  $vat         The VAT of the item
     * @param  float  $discount    The discount of the item
     * @param  float  $total       The total of the item
     * @return $invoice
     */
    public function addItem(string $name, string $description, int $quantity, float $price, float $vat, float $discount, float $total)
    {
        $this->items[] = [
            'name' => $name,
            'description' => $description,
            'quantity' => $quantity,
            'price' => $price,
            'vat' => $vat,
            'discount' => $discount,
            'total' => $total,
        ];

        return $this->invoice->addItem($name, $description, $quantity, $price, $vat, $discount, $total);
    }

    /**
     * Get total without VAT
     */
    public function getTotal()
    {
        $total = 0;
        foreach ($this->items as $item)
        {
            $total += $item['total'];
        }
        return $total;
    }

    /**
     * Get total with VAT
     */
    public function getTotalWithVAT(float $total)
    {
        return $total + ($total * 20 / 100);
    }
}
