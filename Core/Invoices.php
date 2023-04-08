<?php

namespace Niwee\Trident\Core;

use Konekt\PdfInvoice\InvoicePrinter;
use Niwee\Trident\Core\Database;
use Niwee\Trident\Core\PDFGenerator;

/**
 * Class Invoice
 */
final class Invoices
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
     */
    public function generate()
    {
        $pdf = new PDFGenerator();
        $pdf->generate(1,);
    }

    /**
     * Get all invoices
     */
    public static function getAll()
    {
        $db = new Database();
        $self = new self();

        if ($self->count() === 0)
        {
            return "Aucune facture n'a été trouvée.";
        }

        return $db->select('invoices', [
            "[>]quotes" => ["quote_id" => "id"],
            "[>]companies (emitter)" => ["company_emitter" => "id"],
            "[>]companies (receiver)" => ["company_receiver" => "id"],
            "[>]invoices_services (services)" => ["invoice_id" => "id"],
        ], [
            'invoices' => [
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
            ],
            'quotes' => [
                `quotes.id`,
            ],
            'emitter' => [
                `emitter.id`,
                `emitter.name`,
            ],
            'receiver' => [
                `receiver.id`,
                `receiver.name`,
            ],
            ['services' => [
                'services.id',
                'services.name',
                'services.price',
                'services.quantity',
                'services.discount',
                'services.tax',
            ]],
        ], null, null);
    }

    /**
     * @return int|null
     */
    public static function count(): ?int
    {
        $db = new Database();
        return $db->count('invoices', null, null, null);
    }
}
