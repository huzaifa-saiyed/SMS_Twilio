<?php
namespace Kitchen365\Twilio\Setup\Patch\Data;
 
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\Patch\PatchInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
 
class InstallSmsAlertAttribute implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
 
    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * @var QuoteSetupFactory
     */
    protected $quoteSetupFactory;
 
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
 
    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;
 
    /**
     * Constructor
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Config $eavConfig
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param EavSetupFactory $eavSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        Config $eavConfig,
        QuoteSetupFactory $quoteSetupFactory,
        EavSetupFactory $eavSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavConfig = $eavConfig;
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }
 
    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
 
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
 
        $eavSetup->addAttribute('customer_address', 'sms_alert',   [
            'label' => 'SMS Notifications',
            'type' => 'int',
            'input' => 'boolean',
            'required' => false,
            'sort_order' => 125,
            'position' => 125,
            'system' => false,
            'is_user_defined' => true,
            'visible' => true,
            'scopes' => ScopedAttributeInterface::SCOPE_GLOBAL,
            'backend' => '',
            'frontend' => '',
            'source' => '',
            'default' => '1',
            'unique' => false,
            'note' => ''
        ]);
 
        $attribute = $this->eavConfig->getAttribute('customer_address', 'sms_alert');
        $attribute->setData(
            'used_in_forms',
            ['adminhtml_customer_address', 'customer_address_edit', 'customer_register_address']
        );
        $attribute->save();
 
        $this->moduleDataSetup->endSetup();
    }
 
    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }
 
    /**
     * {@inheritdoc}
     */
    public function revert()
    {
        $this->moduleDataSetup->startSetup();
 
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->removeAttribute('customer_address', 'sms_alert');
 
        $this->moduleDataSetup->endSetup();
    }
 
    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}