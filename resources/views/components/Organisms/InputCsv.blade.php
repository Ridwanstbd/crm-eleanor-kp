<div>
    <x-Atoms.Input  
        type="file"
        name="csv_file"
        id="csv_file"
        accept=".csv"
        class="w-full" 
    />
    <x-Atoms.Button variant="secondary" :href={{route('download.csv.template')}} >Download Template CSV</x-Atoms.Button>
</div>