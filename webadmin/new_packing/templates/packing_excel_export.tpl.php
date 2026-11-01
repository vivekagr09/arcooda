<?php
//expecting:
//$container_list

echo '<?xml version="1.0"?>'."\n";
echo '<?mso-application progid="Excel.Sheet"?>'."\n";
?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">
	<Styles>
		<Style ss:ID="Default" ss:Name="Normal">
			<Alignment ss:Horizontal="Center" ss:Vertical="Top" />
			<Font ss:FontName="Times New Roman" x:Family="Roman" ss:Size="9.5" />
		</Style>
		<Style ss:ID="sTitle">
			<Font ss:Size="18" ss:Bold="1" ss:Underline="Single" />
		</Style>
		<Style ss:ID="sDateRow">
			<Alignment ss:Vertical="Bottom" />
			<Font x:Family="Swiss" />
		</Style>
		<Style ss:ID="sPINo">
			<Alignment ss:Horizontal="Left" />
			<Font ss:Size="9" />
		</Style>
		<Style ss:ID="sDate">
			<Alignment ss:Horizontal="Right" />
			<Font ss:Size="14" />
		</Style>
		<Style ss:ID="sSpaceRow">
			<Borders>
				<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="3" />
			</Borders>
		</Style>
		<Style ss:ID="sHeaderRow">
			<Alignment ss:Horizontal="Center" ss:Vertical="Center" />
			<Borders>
				<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" />
				<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" />
			</Borders>
			<Font ss:Size="7" ss:Italic="1" ss:Underline="Single" />
		</Style>
		<Style ss:ID="sItems">
			<Alignment ss:Horizontal="Left" ss:WrapText="1" />
		</Style>
	</Styles>
<?php
$sheet_count=0;
foreach($container_list as $container)
{
	$sheet_count++;
?>
	<Worksheet ss:Name="Sheet<?= $sheet_count ?>">
		<Table ss:ExpandedColumnCount="6" ss:ExpandedRowCount="<?= count($container->crate_list)+7 ?>" x:FullColumns="1" x:FullRows="1" ss:DefaultColumnWidth="53.581395348837205" ss:DefaultRowHeight="12.558139534883722">
			<Column ss:Width="46.04651162790698" />
			<Column /><!-- <Column ss:Width="81.20930232558139" /> -->
			<Column ss:StyleID="sItems" ss:Width="246.97674418604652" />
			<Column ss:Width="37.674418604651166" />
			<Column ss:Width="71.16279069767441" />
			<Column ss:Width="65.302325581395351" />

			<Row ss:AutoFitHeight="0" ss:Height="23">
				<Cell ss:MergeAcross="5" ss:StyleID="sTitle"><Data ss:Type="String">Packing List</Data></Cell>
			</Row>
			<Row ss:AutoFitHeight="0" ss:Height="57" ss:StyleID="sDateRow">
				<Cell ss:MergeAcross="2" ss:StyleID="sPINo"><Data ss:Type="String">P/I NO H80898R4</Data></Cell>
				<Cell ss:MergeAcross="2" ss:StyleID="sDate"><Data ss:Type="String">Date 25-Jul-05</Data></Cell>
			</Row>
			<Row ss:AutoFitHeight="0" ss:Height="21" ss:StyleID="sSpaceRow" />
			<Row ss:AutoFitHeight="0" ss:Height="16" ss:StyleID="sHeaderRow">
				<Cell><Data ss:Type="String">Carton No.</Data></Cell>
				<Cell><Data ss:Type="String">Item#</Data></Cell>
				<Cell><Data ss:Type="String">Description:</Data></Cell>
				<Cell><Data ss:Type="String">Quantity</Data></Cell>
				<Cell><Data ss:Type="String">Measurement(cm)</Data></Cell>
				<Cell><Data ss:Type="String">Gross weight(kg)</Data></Cell>
			</Row>
<?php
	$carton_count=0;
	foreach($container->crate_list as $crate)
	{
		$carton_count++;
?>
			<Row ss:AutoFitHeight="1">
				<Cell><Data ss:Type="String"><?= $carton_count ?></Data></Cell>
				<Cell><Data ss:Type="String"><?= $crate->product_id ?></Data></Cell>
				<Cell><Data ss:Type="String"><?= $crate->name ?></Data></Cell>
				<Cell><Data ss:Type="String">1</Data></Cell>
				<Cell><Data ss:Type="String"><?= $crate->width ?>x<?= $crate->depth ?>x<?= $crate->height ?></Data></Cell>
				<Cell><Data ss:Type="String"><?= $crate->weight ?></Data></Cell>
			</Row>
<?php
	}
?>
		</Table>
		<WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
			<PageSetup>
				<Layout x:CenterHorizontal="1" />
				<PageMargins x:Bottom="0.394" x:Left="0.394" x:Right="0.394" x:Top="0.394" />
			</PageSetup>
		</WorksheetOptions>
	</Worksheet>
<?php
}
?>
</Workbook>
