<?php

namespace Niwee\Trident\Core;

use Konekt\PdfInvoice\InvoicePrinter;
use Niwee\Trident\Core\Database;

/**
 *
 */
final class Quotes
{
    /**
     * @var array
     */
    private $items = [];
    /**
     * @var
     */
    private $invoice;

    /**
     * Generate an Invoice
     *
     * @param int $type The type of pdf to generate (1 = Facture, 2 = Devis, 3 = Accompte)
     */
    public function generate(int $type = 1)
    {
        $this->db = new Database();
        $all_invoices = $this->getAll();
        $this->invoice = new InvoicePrinter('A4', '€', 'fr');

        // Set invoice type
        switch ($type)
        {
            case 2:
                $this->invoice->setType("Devis");
                break;
            case 3:
                $this->invoice->setType("Accompte");
                break;
            default:
                $this->invoice->setType("Facture");
                break;
        }

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
        $this->invoice->setColor("#f28526");

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
        $this->invoice->addBadge("A PAYER");

        // Add note
        $this->invoice->addTitle("Message Important");
        $this->invoice->addParagraph("Ce facture constitue un accompte de la précédente facture que vous avez reçu.");
        $this->invoice->setFooternote("NiWee Productions");

        // Render
        $this->invoice->render($_SERVER['DOCUMENT_ROOT'] . '/static/pdf/invoice.pdf', 'F');
    }

    /**
     * @param  float $totalWithVat
     * @param  float $total
     * @return float
     */
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

    /**
     * @return string|void
     */
    public static function getAll()
    {
        $db = new Database();
        $self = new self();

        if ($self->count() === 0)
        {
            return "Aucun devis n'a été trouvée.";
        }

        $db->select(
            'invoices',
            [
                "[>]quotes" => ["quote_id" => "id"],
                "[>]companies (emitter)" => ["company_emitter" => "id"],
                "[>]companies (receiver)" => ["company_receiver" => "id"],
                "[>]invoices_services (services)" => ["invoice_id" => "id"],
            ],
            [
                `invoices.id`,
                `invoices.format`,
                `invoices.currency_symbol`,
                `invoices.locale`,
                `invoices.timezone`,
                `invoices.logo_path`,
                `invoices.color_accent`,
                `invoices.reference`,
                `invoices.date`,
                `invoices.time`,
                `invoices.due_on`,
                `invoices.company_emitter`,
                `invoices.company_receiver`,
                `invoices.total_amount`,
                `invoices.tax_amount`,
                `invoices.total_with_tax`,
                `invoices.total_discount`,
                `invoices.badge`,
                `invoices.notice_title`,
                `invoices.notice_message`,
                `invoices.footer_note`,
                `invoices.path`,
                `invoices.paid`,
                `invoices.created_at`,
                `invoices.updated_at`,
                `invoices.quote_id`,
                `quotes.id`,
                `emitter.id`,
                `emitter.name`,
                `receiver.id`,
                `receiver.name`,
                `services.id`,
            ],
            function ($data) use ($db)
            {

                // Get services linked to the invoices
                $linked_services = [];
                foreach ($data['services.id'] as $service_id)
                {
                    $invoice_id = $data['invoices.id'];
                    $linked_services[$invoice_id] = $db->select(
                        'services',
                        [
                            'id',
                            'name',
                            'description',
                            'price'
                        ],
                        [
                            'id' => $service_id
                        ]
                    );
                }

                return array_merge($data, $linked_services);
            }
        );
    }

    /**
     * @return int|null
     */
    public static function count(): ?int
    {
        $db = new Database();
        return $db->count('quotes');
    }
}
