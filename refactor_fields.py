import os
import re

directories_to_scan = ['app', 'resources/views', 'database/migrations']

# Cấu trúc ánh xạ (Mapping) từ tiếng Việt sang tiếng Anh
replacements = {
    # ho_sos
    r'\bma_ho_so\b': 'dossier_code',
    r'\bxung_ho\b': 'salutation',
    r'\bten_chu_ho_so\b': 'owner_name',
    r'\bsdt_chu_ho_so\b': 'owner_phone',
    r'\bloai_ho_so_id\b': 'dossier_type_id',
    r'\bloai_thu_tuc_id\b': 'procedure_type_id',
    r'\bxa_id\b': 'ward_id',
    r'\bnguoi_tham_tra_id\b': 'inspector_id',
    r'\bchu_su_dung\b': 'land_owners',
    r'\buy_quyen\b': 'authorization',
    r'\bthua_chung\b': 'shared_plots',
    r'\bngay_cap_gcn\b': 'certificate_issue_date',
    r'\bso_vao_so\b': 'registration_book_number',
    r'\bso_phat_hanh\b': 'publication_number',
    r'\bxa_ap_thon\b': 'address_details',
    r'\bthong_tin_rieng\b': 'private_info',
    r'\bhan_giai_quyet\b': 'deadline',
    r'\bghi_chu\b': 'notes',
    r'\btrang_thai\b': 'status',
    
    # JSON keys inside land_owners / shared_plots
    r'\bho_ten\b': 'full_name',
    r'\bngay_sinh\b': 'date_of_birth',
    r'\bcccd\b': 'id_card',
    r'\bngay_cap\b': 'issue_date',
    r'\bdia_chi\b': 'address',
    r'\bnguoi\b': 'person',
    r'\bgiay\b': 'paper',
    # 'to' and 'thua' can be risky to replace globally, let's target 'to\b' within quotes if possible
    r"['\"]to['\"]": "'map_sheet'",
    r"['\"]thua['\"]": "'plot_number'",
    r"\bto\b(?=\s*=>)": "map_sheet", # for associative arrays
    r"\bthua\b(?=\s*=>)": "plot_number", # for associative arrays
    r'\bdien_tich\b': 'area',
    r'\bap_thon\b': 'hamlet',
    r'\bloai\b': 'type',
    r'\bnguoi_lien_quan\b': 'related_person',
    r'\bngay_cap_cccd\b': 'id_issue_date',
    
    # loai_thu_tucs
    r'\bngay_tra_ket_qua\b': 'processing_days',
    
    # ho_so_files
    r'\bten_file\b': 'file_name',
    r'\bduong_dan\b': 'file_path',
    r'\bloai_file\b': 'file_type',
    r'\bkich_thuoc\b': 'file_size',
    
    # ho_so_trang_thai_logs
    r'\btrang_thai_cu\b': 'old_status',
    r'\btrang_thai_moi\b': 'new_status',
    
    # so_theo_doi_groups
    r'\bten_so\b': 'book_name',
    r'\bma_so\b': 'book_code',
    r'\bmo_ta\b': 'description',
    r'\bnguoi_tao_id\b': 'creator_id',
    
    # ho_so_so_theo_doi
    r'\bso_theo_doi_group_id\b': 'tracking_book_id',
    r'\bthu_tu\b': 'order_index',
    
    # mau_word_folders & mau_words
    r'\bfile_dinh_kem\b': 'attachment',
}

# Add specifically bounded replaces for "ten" -> "name" to avoid replacing general words like 'extend' or 'content'
# We replace object->ten, ['ten'], "ten", etc.
replacements[r"->ten\b"] = "->name"
replacements[r"['\"]ten['\"]"] = "'name'"
replacements[r"\bten\b(?=\s*=>)"] = "name"

def process_file(file_path):
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
        
    new_content = content
    for pattern, repl in replacements.items():
        new_content = re.sub(pattern, repl, new_content)
        
    if new_content != content:
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated: {file_path}")

for directory in directories_to_scan:
    for root, _, files in os.walk(directory):
        for file in files:
            if file.endswith('.php') or file.endswith('.blade.php'):
                process_file(os.path.join(root, file))
