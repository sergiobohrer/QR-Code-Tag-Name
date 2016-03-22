<?php
require_once( 'QRCode.class.php' );

class vCards
{
    private $lines = '';

    public function __construct( $filePath = 'participantes.csv' )
    {
        if ( file_exists( $filePath ) )
        {
            $this->lines = file( $filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
        }
    }

    private function getCard( $lineInfo )
    {
        $name = trim( $lineInfo[0], "\"'" );
        $email = trim( $lineInfo[1], "\"'" );
        $organization = trim( $lineInfo[2], "\"'" );
        $ticket = ( isset( $lineInfo[3] ) ? trim( $lineInfo[3], "\"'" ) : '' );

        $oQRC = new QRCode;
        $oQRC->name( $name ); // Add Name
        $oQRC->email( $email ); // Add E-mail
        $oQRC->organization( $organization ); // Organization
        $oQRC->note( 'VR Dev Summit 2016' ); // Note
        $oQRC->finish(); // End vCard

        $retVal = '<div class="card">';
        $retVal .= '<p class="name ' . ( empty( $organization ) ? 'bigger' : '' ) . '">' . $name . '</p>';
        $retVal .= ( ( ! empty( $organization ) ) ? ( '<p class="org">' . $organization . '</p>' ) : '' );
        $retVal .= '<div class="qrcode">';
        $retVal .= '<img src="' . $oQRC->get( 200, 'L' ) . '" alt="QR Code" />';
        $retVal .= '</div>';
        $retVal .= '<p class="ticket">' . $ticket . '</p>';
        $retVal .= '</div>';

        return $retVal;
    }

    public function getAllCards()
    {
        $retVal = "";

        if ( count( $this->lines ) > 1 )
        {
            for ( $i = 1; $i < count( $this->lines ); $i++ )
            {
                $aData = explode( ';', $this->lines[$i] );

                if ( count( $aData ) < 2 || empty( $aData[0] ) )
                {
                    continue;
                }

                $retVal .= ( ( $i % 3 ) != 0 ? '<div class="row">' : '' );
                $retVal .= $this->getCard( $aData );
                $retVal .= ( ( $i % 3 ) == 0 ? '</div>' : '');
            }
        }

        return $retVal;
    }
};

function getVCards()
{
    $data = new vCards();
    return $data->getAllCards();
}