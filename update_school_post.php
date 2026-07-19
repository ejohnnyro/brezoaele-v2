<?php
/**
 * Temp script to update school post details to match official MFE records.
 */
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );

$post_id = 2024;
$post = get_post( $post_id );
if ( !$post ) {
    echo "Post 2024 not found!";
    exit;
}

// 1. Update Title and Content
$post_data = array(
    'ID'           => $post_id,
    'post_title'   => 'Proiectul de Construire a Noii Școli Gimnaziale din Brezoaele',
    'post_content' => '<p style="text-align: justify;">Unul dintre cele mai ambițioase proiecte de infrastructură din istoria comunei Brezoaele se află în plină desfășurare. Este vorba despre <strong>construirea unei școli moderne</strong>, care va înlocui actuala unitate de învățământ din satul Brezoaia, aducând un salt calitativ major în educația copiilor din comună.</p>

<h3>Obiectivul și Scopul Proiectului</h3>
<p style="text-align: justify;"><strong>Obiectivul general al proiectului:</strong> Îmbunătățirea calității infrastructurii Școlii Gimnaziale Brezoaia-Brezoaele, combaterea sărăciei și a excluziunii sociale din comunitatea Brezoaele, județul Dâmbovița, prin implementarea de acțiuni care să înlăture situația de nesiguranță și abandon școlar, contribuind la reducerea diferențelor urban-rural în domeniul educațional.</p>
<p style="text-align: justify;">Infrastructura educațională este un element esențial al procesului educațional, având un efect direct asupra rezultatelor elevilor, inclusiv participarea, retenția, motivația și performanțele acestora. Pentru asigurarea unui acces echitabil la medii de învățare sigure, solicitantul <strong>UAT Comuna Brezoaele</strong> dorește să asigure spații fizice protejate, sigure și moderne care să faciliteze predarea și învățarea la standarde europene.</p>

<h3>Date Financiare și Structura Bugetului</h3>
<p style="text-align: justify;">Proiectul este finanțat prin Programul Regional Sud-Muntenia (Prioritatea P5. O regiune educată), prin intermediul Fondului European de Dezvoltare Regională (FEDR) și al bugetului de stat. Structura financiară este detaliată în continuare:</p>

<table style="width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 20px;">
    <thead>
        <tr style="background-color: #f1f5f9;">
            <th style="border: 1px solid var(--color-border); padding: 10px; text-align: left; font-family: var(--font-heading);">Indicator bugetar</th>
            <th style="border: 1px solid var(--color-border); padding: 10px; text-align: right; font-family: var(--font-heading);">Valoare (RON)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border: 1px solid var(--color-border); padding: 10px;"><strong>Valoare Totală Proiect</strong></td>
            <td style="border: 1px solid var(--color-border); padding: 10px; text-align: right; font-weight: bold;">39.219.289,27 RON</td>
        </tr>
        <tr>
            <td style="border: 1px solid var(--color-border); padding: 10px;"><strong>Buget Total Eligibil</strong></td>
            <td style="border: 1px solid var(--color-border); padding: 10px; text-align: right; font-weight: bold;">20.200.363,45 RON</td>
        </tr>
        <tr>
            <td style="border: 1px solid var(--color-border); padding: 10px; padding-left: 25px;">— Fondul European de Dezvoltare Regională (FEDR)</td>
            <td style="border: 1px solid var(--color-border); padding: 10px; text-align: right; color: var(--color-text-muted);">9.692.134,39 RON</td>
        </tr>
        <tr>
            <td style="border: 1px solid var(--color-border); padding: 10px; padding-left: 25px;">— Bugetul de Stat al României</td>
            <td style="border: 1px solid var(--color-border); padding: 10px; text-align: right; color: var(--color-text-muted);">10.104.221,80 RON</td>
        </tr>
        <tr>
            <td style="border: 1px solid var(--color-border); padding: 10px; padding-left: 25px;">— Cofinanțare locală (UAT Comuna Brezoaele)</td>
            <td style="border: 1px solid var(--color-border); padding: 10px; text-align: right; color: var(--color-text-muted);">404.007,27 RON</td>
        </tr>
        <tr>
            <td style="border: 1px solid var(--color-border); padding: 10px;"><strong>Buget Neeligibil Total</strong></td>
            <td style="border: 1px solid var(--color-border); padding: 10px; text-align: right;">19.018.925,82 RON</td>
        </tr>
    </tbody>
</table>

<h3>Detalii Administrative</h3>
<ul style="line-height: 1.6;">
    <li><strong>Cod SMIS Proiect:</strong> 338324</li>
    <li><strong>Autoritate Responsabilă:</strong> Agenția pentru Dezvoltare Regională Sud-Muntenia (ADRSM)</li>
    <li><strong>Beneficiar / Lider Proiect:</strong> UAT COMUNA BREZOAELE</li>
    <li><strong>Tipul Apelului:</strong> Competitiv cu termen-limită de depunere (PRSM/310/PRSM_P5/OP4/RSO4.2/PRSM_A23)</li>
    <li><strong>Data de Începere:</strong> 01.08.2024</li>
    <li><strong>Data de Finalizare estimată:</strong> 28.02.2029</li>
    <li><strong>Status Curent:</strong> În derulare</li>
</ul>'
);

$res = wp_update_post( $post_data, true );

if ( is_wp_error( $res ) ) {
    echo "Error updating post: " . $res->get_error_message();
    exit;
}

// 2. Update Metadata
update_post_meta( $post_id, '_investitie_stadiu', 'În derulare' );
update_post_meta( $post_id, '_investitie_buget', '39.219.289,27 RON' );
update_post_meta( $post_id, '_investitie_sursa', 'Programul Regional Sud Muntenia (FEDR / Buget de Stat)' );

echo "Successfully updated post 2024 content and metadata!";
