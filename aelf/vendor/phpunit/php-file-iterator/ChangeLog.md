ransactionOutputInterface $txOut
     * @param SignData $signData
     * @param CheckerBase $checker
     * @param TransactionSignatureSerializer|null $sigSerializer
     * @param PublicKeySerializerInterface|null $pubKeySerializer
     */
    public function __construct(
        EcAdapterInterface $ecAdapter,
        TransactionInterface $tx,
        int $nInput,
        TransactionOutputInterface $txOut,
        SignData $signData,
        CheckerBase $checker,
        TransactionSignatureSerializer $sigSerializer = null,
        PublicKeySerializerInterface $pubKeySerializer = null
    ) {
        $this->ecAdapter = $ecAdapter;
        $this->tx = $tx;
        $this->nInput = $nInput;
        $this->txOut = $txOut;
        $this->signData = $signData;

        $defaultFlags = Interpreter::VERIFY_DERSIG | Interpreter::VERIFY_P2SH | Interpreter::VERIFY_CHECKLOCKTIMEVERIFY | Interpreter::VERIFY_CHECKSEQUENCEVERIFY | Interpreter::VERIFY_WITNESS;
        $this->flags = $this->signData->hasSignaturePolicy() ? $this->signData->getSignaturePolicy() : $defaultFlags;

        $this->txSigSerializer = $sigSerializer ?: new TransactionSignatureSerializer(EcSerializer::getSerializer(DerSignatureSerializerInterface::class, true, $ecAdapter));
        $this->pubKeySerializer = $pubKeySerializer ?: EcSerializer::getSerializer(PublicKeySerializerInterface::